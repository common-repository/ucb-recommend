<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

abstract class UCBRecommend_API_Base extends \UCBRecommendBase\UCBRecommend_Base_Class
{
	abstract public function get_api_name();

	abstract public function get_method();

	public function get_capability()
	{
		return false;
	}

	protected function admin_filter()
	{
		return true;
	}

	protected function front_filter()
	{
		return true;
	}

	protected function only_loggedin()
	{
		return false;
	}

	protected function only_not_loggedin()
	{
		return false;
	}

	protected function only_front()
	{
		return false;
	}

	protected function only_admin()
	{
		return false;
	}

	public function is_basic_api()
	{
		return false;
	}

	protected function is_form_data()
	{
		return false;
	}

	protected function process_data()
	{
		return null;
	}

	protected function content_type()
	{
		return null;
	}

	protected function data_type()
	{
		return null;
	}

	public function action()
	{
		wp_send_json( $this->get_response() );
	}

	protected function get_response()
	{
		return array();
	}

	protected function external_access()
	{
		return false;
	}

	protected function allowed_origin()
	{
		return null;
	}

	protected function setup_function()
	{

	}

	public function setup()
	{
		if ( !$this->defined( 'DOING_AJAX' ) ) {
			global $ucbr_minify;
			if ( is_admin() ) {
				if ( $this->only_front() ) {
					return false;
				}
				if ( $this->only_not_loggedin() ) {
					return false;
				}
				if ( !$this->admin_filter() ) {
					return false;
				}
			} else {
				if ( $this->only_admin() ) {
					return false;
				}
				if ( !$this->front_filter() ) {
					return false;
				}
			}
			$ucbr_minify->register_script( $this->get_output_js() );
		}

		$this->register_action();
		$this->setup_function();
		return true;
	}

	protected function need_nonce_check()
	{
		return $this->definedv( 'UCB_RECOMMEND_NEED_NONCE_CHECK' ) == true;
	}

	private function is_post()
	{
		return strtolower( trim( $this->get_method() ) ) == "post";
	}

	protected function nonce_check()
	{
		if ( "nonce" === $this->get_api_name() ) {
			return false;
		}
		return $this->apply_filters( "nonce_check", $this->is_post() || $this->need_nonce_check(), $this->get_api_name(), $this->is_post(), $this->need_nonce_check() );
	}

	private function do_nonce_check()
	{
		return isset( $_REQUEST[$this->nonce_key()] ) && wp_verify_nonce( $_REQUEST[$this->nonce_key()], $this->get_api_name() );
	}

	private function nonce_key()
	{
		return $this->get_api_name() . "_nonce";
	}

	final public function get_api_full_name()
	{
		return self::get_prefix() . $this->get_api_name();
	}

	private function get_output_js()
	{
		if ( $this->nonce_check() ) {
			return $this->get_output_js_nonce();
		}

		$ret = <<< EOS
<script>
	var ucbr_obj = ucbr_obj || {};
	ucbr_obj.{$this->get_api_name()} = function( data, done, fail, always ){

EOS;

		if ( $this->is_form_data() ) {
			$ret .= <<< EOS
		var d = data;
		d.append('action', '{$this->get_api_full_name()}');
EOS;
		} else {
			$ret .= <<< EOS
		var d = data || {};
		d.action = '{$this->get_api_full_name()}';
EOS;
		}
		$ret .= <<< EOS

		return jQuery.ajax({
			type: '{$this->get_method()}',
			url: ucbr_obj.ajaxurl,
			data: d
EOS;
		if ( null !== $this->data_type() ) {
			if ( !in_array( $this->data_type(), array( "true", "false" ) ) ) {
				$ret .= ",\n				contentType: '{$this->data_type()}'";
			} else {
				$ret .= ",\n				contentType: {$this->data_type()}";
			}
		}
		if ( null !== $this->content_type() ) {
			if ( !in_array( $this->content_type(), array( "true", "false" ) ) ) {
				$ret .= ",\n				contentType: '{$this->content_type()}'";
			} else {
				$ret .= ",\n				contentType: {$this->content_type()}";
			}
		}
		if ( null !== $this->process_data() ) {
			if ( !in_array( $this->process_data(), array( "true", "false" ) ) ) {
				$ret .= ",\n				processData: '{$this->process_data()}'";
			} else {
				$ret .= ",\n				processData: {$this->process_data()}";
			}
		}
		$ret .= <<< EOS

		}).done( function( res ){
			if( done ) done( res );
		}).fail( function( xhr, status, error ){
			if( fail ) fail( error );
		}).always( function( ){
			if( always ) always( );
		});
	};
</script>
EOS;

		return $ret;
	}

	private function get_output_js_nonce()
	{
		if ( $this->consider_page_cache() ) {
			return $this->get_output_js_nonce2();
		}

		$nonce = wp_create_nonce( $this->get_api_name() );
		$ret = <<< EOS
<script>
	var ucbr_obj = ucbr_obj || {};
	ucbr_obj.{$this->nonce_key()} = '{$nonce}';
	ucbr_obj.{$this->get_api_name()} = function( data, done, fail, always ){
EOS;

		if ( $this->is_form_data() ) {
			$ret .= <<< EOS
		var d = data;
		d.append('action', '{$this->get_api_full_name()}');
		d.append('{$this->nonce_key()}', ucbr_obj.{$this->nonce_key()});
EOS;
		} else {
			$ret .= <<< EOS
		var d = data || {};
		d.action = '{$this->get_api_full_name()}';
		d.{$this->nonce_key()} = ucbr_obj.{$this->nonce_key()};
EOS;
		}
		$ret .= <<< EOS

		return jQuery.ajax({
			type: '{$this->get_method()}',
			url: ucbr_obj.ajaxurl,
			data: d
EOS;
		if ( null !== $this->data_type() ) {
			if ( !in_array( $this->data_type(), array( "true", "false" ) ) ) {
				$ret .= ",\n			contentType: '{$this->data_type()}'";
			} else {
				$ret .= ",\n			contentType: {$this->data_type()}";
			}
		}
		if ( null !== $this->content_type() ) {
			if ( !in_array( $this->content_type(), array( "true", "false" ) ) ) {
				$ret .= ",\n			contentType: '{$this->content_type()}'";
			} else {
				$ret .= ",\n			contentType: {$this->content_type()}";
			}
		}
		if ( null !== $this->process_data() ) {
			if ( !in_array( $this->process_data(), array( "true", "false" ) ) ) {
				$ret .= ",\n			processData: '{$this->process_data()}'";
			} else {
				$ret .= ",\n			processData: {$this->process_data()}";
			}
		}
		$ret .= <<< EOS

		}).done( function( res ){
			if( done ) done( res );
		}).fail( function( xhr, status, error ){
			if( fail ) fail( error );
		}).always( function( ){
			if( always ) always( );
		});
	};
</script>
EOS;

		return $ret;
	}

	private function get_output_js_nonce2()
	{
		$ret = <<< EOS
<script>
	var ucbr_obj = ucbr_obj || {};
	ucbr_obj.{$this->get_api_name()} = function( data, done, fail, always ){
		if (ucbr_obj.{$this->nonce_key()}) {

EOS;

		if ( $this->is_form_data() ) {
			$ret .= <<< EOS
			var d = data;
			d.append('action', '{$this->get_api_full_name()}');
			d.append('{$this->nonce_key()}', ucbr_obj.{$this->nonce_key()});
EOS;
		} else {
			$ret .= <<< EOS
			var d = data || {};
			d.action = '{$this->get_api_full_name()}';
			d.{$this->nonce_key()} = ucbr_obj.{$this->nonce_key()};
EOS;
		}
		$ret .= <<< EOS

			return jQuery.ajax({
				type: '{$this->get_method()}',
				url: ucbr_obj.ajaxurl,
				data: d
EOS;
		if ( null !== $this->data_type() ) {
			if ( !in_array( $this->data_type(), array( "true", "false" ) ) ) {
				$ret .= ",\n				contentType: '{$this->data_type()}'";
			} else {
				$ret .= ",\n				contentType: {$this->data_type()}";
			}
		}
		if ( null !== $this->content_type() ) {
			if ( !in_array( $this->content_type(), array( "true", "false" ) ) ) {
				$ret .= ",\n				contentType: '{$this->content_type()}'";
			} else {
				$ret .= ",\n				contentType: {$this->content_type()}";
			}
		}
		if ( null !== $this->process_data() ) {
			if ( !in_array( $this->process_data(), array( "true", "false" ) ) ) {
				$ret .= ",\n				processData: '{$this->process_data()}'";
			} else {
				$ret .= ",\n				processData: {$this->process_data()}";
			}
		}
		$ret .= <<< EOS

			}).done( function( res ){
				if( done ) done( res );
			}).fail( function( xhr, status, error ){
				if( fail ) fail( error );
			}).always( function( ){
				if( always ) always( );
			});
		} else {
			var obj = {};
			var ajax = ucbr_obj.nonce({name:'{$this->get_api_name()}'}, function(res){
				if (res.nonce) {
					ucbr_obj.{$this->nonce_key()} = res.nonce;
					ajax = ucbr_obj.{$this->get_api_name()}(data, done, fail, always);
				} else {
					if( fail ) fail( res );
				}
			}, function(error){
				if( fail ) fail( error );
				if( always ) always( );
			});
			obj.abort = function() {
				ajax.abort();
			};
			return obj;
		}
	};
</script>
EOS;

		return $ret;
	}

	final public function output_js()
	{
		echo $this->get_output_js();
	}

	final public function ajax_action()
	{
		if ( $this->nonce_check() ) {
			if ( !$this->do_nonce_check() ) {
				status_header( '403' );
				echo 'Forbidden';
				die;
			}
		} else {
			if ( $this->apply_filters( 'check_referer', UCB_RECOMMEND_CHECK_REFERER ) ) {
				if ( !isset( $_SERVER['HTTP_REFERER'] ) ) {
					$referer = '';
				} else {
					$referer = $_SERVER['HTTP_REFERER'];
					$referer = parse_url( $referer );
					$referer = false === $referer ? '' : $referer['host'];
				}
				$host = isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
				if ( !stristr( $referer, $host ) ) {
					status_header( '403' );
					echo 'Forbidden';
					die;
				}
			}
		}

		$capability = $this->get_capability();
		if ( is_null( $capability ) || is_string( $capability ) ) {
			global $ucbr_user;
			if ( !$ucbr_user->loggedin ) {
				status_header( '403' );
				echo 'Forbidden';
				die;
			}
			if ( !$ucbr_user->user_can( $capability ) ) {
				status_header( '403' );
				echo 'Forbidden';
				die;
			}
		}

		if ( $this->external_access() ) {
			$origins = $this->allowed_origin();
			if ( is_array( $origins ) ) {
				if ( isset( $_SERVER['HTTP_ORIGIN'] ) && in_array( $_SERVER['HTTP_ORIGIN'], $origins ) ) {
					header( "Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN'] );
					header( "Access-Control-Allow-Credentials: true" );
					header( "Access-Allow-Control-Headers: X-Requested-With, Authorization" );
				} else {
					status_header( '403' );
					echo 'Forbidden';
					die;
				}
			} else {
				header( "Access-Control-Allow-Origin: *" );
			}
		}
		$this->action();
	}

	final public function register_action()
	{
		if ( !$this->only_loggedin() ) {
			add_action( 'wp_ajax_' . $this->get_api_full_name(), array( $this, 'ajax_action' ) );
		}
		if ( !$this->only_not_loggedin() ) {
			add_action( 'wp_ajax_nopriv_' . $this->get_api_full_name(), array( $this, 'ajax_action' ) );
		}
	}

	public static function get_prefix()
	{
		return "ucbr_api_";
	}

	public static function get_slug( $file )
	{
		return str_replace( "-", "_", preg_replace( "/^(.+)\\.php$/", "$1", basename( $file ) ) );
	}

	public static function get_name( $file )
	{
		return self::get_prefix() . self::get_slug( $file );
	}

}
