<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Test extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	protected function __construct()
	{
		$this->initialize();
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Test();
		}
		return self::$_instance;
	}

	private function initialize()
	{
//		add_action( 'ucbr_changed_option', function ( $key ) {
//			if ( $this->get_filter_prefix() . 'front_admin_ajax' === $key ||
//				$this->get_filter_prefix() . 'check_referer' === $key
//			) {
//				add_action( 'admin_footer', function () {
//					$this->undone();
//				} );
//			}
//		} );

		add_action( 'admin_head', function () {
			$fatal = false;
			if ( !$this->apply_filters( 'test', UCB_RECOMMEND_TEST ) ) {
				global $ucbr_option;
				$fatal = $ucbr_option->get( 'fatal_error' );
				if ( $fatal ) {
					$this->error( sprintf( __( "<strong>%s</strong> doesn't work on your server.", UCB_RECOMMEND_TEXT_DOMAIN ), UCB_RECOMMEND_PLUGIN_NAME ) );
				} else {
					return;
				}
			}
			global $ucbr_api;
			foreach ( $this->get_use_methods() as $method ) {
				$ucbr_api->register_use_function( $method );
			}
			$ucbr_api->register_use_function( 'get-tests' );
			$ucbr_api->register_use_function( 'reflect-results' );

			$loading_file = $this->apply_filters( "loading_image", UCB_RECOMMEND_LIB_IMG_DIR . DIRECTORY_SEPARATOR . UCB_RECOMMEND_LOADING_IMAGE, -2 );
			$loading_file = $this->dir2path( $loading_file );

			$back_file = $this->apply_filters( "back_image", UCB_RECOMMEND_LIB_IMG_DIR . DIRECTORY_SEPARATOR . 'back.png' );
			$back_file = $this->dir2path( $back_file );

			global $ucbr_minify;
			$ucbr_minify->register_script( $this->view( 'modal-script', false, array(), true ) );
			$ucbr_minify->register_script( $this->view( 'test-script', false, array( 'loading_image' => $loading_file ) ) );
			$ucbr_minify->register_css( $this->view( 'modal-style', false, array( 'loading_image' => $loading_file, "back_file" => $back_file ), true ) );

			if ( $fatal ) {
				$this->error( $this->view( 'test', false, array( 'retest' => $fatal ) ) );
			} else {
				$this->message( $this->view( 'test', false, array( 'retest' => $fatal ) ) );
			}
		} );
	}

	public function undone()
	{
		global $ucbr_option;
		$ucbr_option->set( $this->get_filter_prefix() . 'test', 'true' );
	}

	public function done()
	{
		global $ucbr_option;
		$ucbr_option->set( $this->get_filter_prefix() . 'test', 'false' );
	}

	public function get_test_settings()
	{
		return $this->apply_filters( 'get_test_settings', array(
			'check_ajax' => array(
				'title' => __( 'Ajax test', UCB_RECOMMEND_TEXT_DOMAIN ),
				//								'groups' => array(
				//									'front' => array(
				//										'title' => __( 'Frontend test', UCB_RECOMMEND_TEXT_DOMAIN ),
				'items' => array(
					array(
						'front' => true,
						'admin' => false,
						'referer' => true
					),
					array(
						'front' => true,
						'admin' => true,
						'referer' => true
					),
					array(
						'front' => true,
						'admin' => false,
						'referer' => false
					),
					array(
						'front' => true,
						'admin' => true,
						'referer' => false
					),
				)
			),
			//								'back' => array(
			//									'title' => __( 'Backend test', UCB_RECOMMEND_TEXT_DOMAIN ),
			//									'items' => array(
			//										array(
			//											'front' => false,
			//											'admin' => true,
			//											'referer' => true
			//										),
			//										array(
			//											'front' => false,
			//											'admin' => true,
			//											'referer' => false
			//										),
			//									)
			//								)
			//							)
			//						),
		) );
	}

	public function get_use_methods()
	{
		return array_keys( $this->get_test_settings() );
	}

	public function reflect_result( $test_results )
	{
		if ( !is_array( $test_results ) ) {
			return null;
		}

		if ( isset( $test_results['check_ajax'] ) ) {
			$check_ajax = $this->reflect_ajax_result( $test_results['check_ajax'] );
			$fatal = $check_ajax[0];
			$check_ajax = $check_ajax[1];
		} else {
			$fatal = true;
			$check_ajax = array();
		}

		$results = array_merge( $check_ajax );
		global $ucbr_option;
		$ucbr_option->set( 'fatal_error', $fatal );
		$ucbr_option->set( 'test_results', serialize( $results ) );

		$this->done();

		return array(
			'fatal' => $fatal,
			'results' => $results,
			'urls' => array(
				'plugin' => admin_url( 'plugins.php' ),
				'setting' => admin_url( 'admin.php?page=ucbr-setting' ),
			)
		);
	}

	private function reflect_ajax_result( $result )
	{
		global $ucbr_option;

		$front_admin_ajax = $this->apply_filters( 'front_admin_ajax', UCB_RECOMMEND_FRONT_ADMIN_AJAX );
		$check_referer = $this->apply_filters( 'check_referer', UCB_RECOMMEND_CHECK_REFERER );

		$front_result_message = __( "There's no problem", UCB_RECOMMEND_TEXT_DOMAIN );
		$fatal_error = false;

		$s1 = __( 'Whether to use admin-ajax.php on front page', UCB_RECOMMEND_TEXT_DOMAIN );
		$s2 = __( 'Whether to check referer when ajax access without nonce check', UCB_RECOMMEND_TEXT_DOMAIN );
		$front_admin_ajax_result = $front_admin_ajax;
		$check_referer_result = $check_referer;

		if ( $result['check_ajax'][0] ) {
			$front_admin_ajax_result = false;
			$check_referer_result = true;
		} elseif ( $result['check_ajax'][1] ) {
			$front_admin_ajax_result = true;
			$check_referer_result = true;
		} elseif ( $result['check_ajax'][2] ) {
			$front_admin_ajax_result = false;
			$check_referer_result = false;
		} elseif ( $result['check_ajax'][3] ) {
			$front_admin_ajax_result = true;
			$check_referer_result = false;
		} else {
			$fatal_error = true;
			$front_result_message = sprintf( __( "<strong>%s</strong> doesn't work on your server.", UCB_RECOMMEND_TEXT_DOMAIN ), UCB_RECOMMEND_PLUGIN_NAME );
		}

		$changed_message = false;
		if ( $front_admin_ajax !== $front_admin_ajax_result ) {
			$changed_message = sprintf( __( 'Changed [%s] to [%s]', UCB_RECOMMEND_TEXT_DOMAIN ), $s1, var_export( $front_admin_ajax_result, true ) );
			$ucbr_option->set( $this->get_filter_prefix() . 'front_admin_ajax', var_export( $front_admin_ajax_result, true ) );
		}
		if ( $check_referer !== $check_referer_result ) {
			if ( false !== $changed_message ) {
				$changed_message .= '<br>';
			} else {
				$changed_message = '';
			}
			$changed_message .= sprintf( __( 'Changed [%s] to [%s]', UCB_RECOMMEND_TEXT_DOMAIN ), $s2, var_export( $check_referer_result, true ) );
			$ucbr_option->set( $this->get_filter_prefix() . 'check_referer', var_export( $check_referer_result, true ) );
		}
		if ( false !== $changed_message ) {
			$front_result_message = $changed_message;
		}

		return array(
			$fatal_error,
			array(
				'check_ajax' => array(
					'result' => false === $fatal_error,
					'message' => $front_result_message,
				),
			)
		);
	}
}

$GLOBALS['ucbr_test'] = UCBRecommend_Test::get_instance();
