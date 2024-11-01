<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_CheckAjax extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_CheckAjax();
		}
		return self::$_instance;
	}

	protected function need_nonce_check()
	{
		return true;
	}

	public function get_api_name()
	{
		return "check_ajax";
	}

	public function get_method()
	{
		return "post";
	}

	public function get_capability()
	{
		return null;
	}

	protected function only_admin()
	{
		return true;
	}

	protected function setup_function()
	{
		if ( $this->defined( 'DOING_AJAX' ) ) {
			add_action( 'wp_ajax_nopriv_ucbr_check_front_ajax', function () {
				$data = $this->check_referer();
				$data['front'] = true;
				wp_send_json_success( $data );
			} );
			add_action( 'wp_ajax_ucbr_check_back_ajax', function () {
				$data = $this->check_referer();
				$data['back'] = true;
				wp_send_json_success( $data );
			} );
		}
	}

	private function check_referer()
	{
		if ( !isset( $_SERVER['HTTP_REFERER'] ) ) {
			$referer = '';
		} else {
			$referer = $_SERVER['HTTP_REFERER'];
			$referer = parse_url( $referer );
			$referer = false === $referer ? '' : $referer['host'];
		}

		$host = isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
		$result = false !== stristr( $referer, $host );
		return array( 'result' => $result, 'host' => $host, 'referer' => $referer );
	}

	public function get_response()
	{
		$start = microtime( true );
		$elapsed = function ( $start ) {
			return round( microtime( true ) - $start, 6 ) * 1000 . ' ms';
		};

		$this->filter_request('front');
		$this->filter_request('admin');
		$this->filter_request('referer');

		if ( !$_REQUEST["front"] ) {
			$_REQUEST["admin"] = true;
		}

		global $ucbr_ajax;
		$ajaxurl = $ucbr_ajax->get_ajax_url( $_REQUEST["admin"] );
		if ( !$_REQUEST["front"] ) {
			$cookies = array();
			foreach ( $_COOKIE as $name => $value ) {
				$cookies[] = new \WP_Http_Cookie( array( 'name' => $name, 'value' => $value ) );
			}
			$query = array(
				'action' => 'ucbr_check_back_ajax'
			);
			$args = array(
				'body' => http_build_query( $query ),
				'cookies' => $cookies,
			);
		} else {
			$query = array(
				'action' => 'ucbr_check_front_ajax'
			);
			$args = array(
				'body' => http_build_query( $query )
			);
		}
		$request = wp_remote_post(
			$ajaxurl,
			$args
		);

		$result = false;
		if ( is_wp_error( $request ) ) {
			$message = $request->get_error_message();
		} elseif ( 200 != wp_remote_retrieve_response_code( $request ) ) {
			$message = wp_remote_retrieve_response_message( $request );
		} elseif ( isset( $request['body'] ) ) {
			$data = json_decode( $request['body'] );
			if ( 0 === $data ) {
				$message = __( 'Unexpected error', UCB_RECOMMEND_TEXT_DOMAIN ) . ' (0)';
			} elseif ( false === $data || is_null( $data ) ) {
				$message = __( 'Unexpected error', UCB_RECOMMEND_TEXT_DOMAIN ) . ' (json decode)';
			} elseif ( $data->success ) {
				if ( $_REQUEST["front"] && $data->data->front ) {
					$result = true;
					$message = 'success';
				} elseif ( !$_REQUEST["front"] && $data->data->back ) {
					$result = true;
					$message = 'success';
				} else {
					$message = __( 'Unexpected error', UCB_RECOMMEND_TEXT_DOMAIN ) . ' (has no data)';
				}
				if ( $result && $_REQUEST["referer"] ) {
					if ( !$data->data->result ) {
						$result = false;
						$message = __( 'Referer check error', UCB_RECOMMEND_TEXT_DOMAIN );
						$message .= ' (ref:' . $data->data->referer . ', host:' . $data->data->host . ')';
					}
				}
			} else {
				$message = __( 'Unexpected error', UCB_RECOMMEND_TEXT_DOMAIN ) . ' (has no flag)';
			}
		} else {
			$message = __( 'Unexpected error', UCB_RECOMMEND_TEXT_DOMAIN ) . ' (has no body)';
		}

		return array(
			"result" => $result,
			"message" => $message,
			"elapsed" => $elapsed( $start )
		);
	}

	private function filter_request( $name )
	{
		if ( !isset( $_REQUEST[$name] ) ) {
			$_REQUEST[$name] = false;
		} elseif ( 'true' === $_REQUEST[$name] ) {
			$_REQUEST[$name] = true;
		} elseif ( 'false' === $_REQUEST[$name] ) {
			$_REQUEST[$name] = false;
		}
		$_REQUEST[$name] = $_REQUEST[$name] ? true : false;
	}
}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_CheckAjax::get_instance();
