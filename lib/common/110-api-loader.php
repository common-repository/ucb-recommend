<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_API_Loader extends \UCBRecommendBase\UCBRecommend_Base_Class
{

	private static $_instance = null;

	private $use_functions = array();

	private function __construct()
	{
		add_action( 'init', function () {
			if ( $this->defined( 'DOING_AJAX' ) ) {
				$this->setup();
			} else {
				if ( is_admin() ) {
					add_action( 'admin_footer', array( $this, 'setup' ) );
				} else {
					add_action( 'wp_footer', array( $this, 'setup' ) );
				}
			}
		} );
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_API_Loader();
		}
		return self::$_instance;
	}

	public function register_use_function( $name )
	{
		if ( !is_array( $this->use_functions ) ) {
			$this->use_functions = array();
		}
		$name = str_replace( "-", "_", $name );
		if ( !in_array( $name, $this->use_functions ) ) {
			$this->use_functions[] = $name;
		}
	}

	public function setup()
	{
		foreach ( scandir( UCB_RECOMMEND_LIB_API_DIR ) as $file ) {
			if ( preg_match( "/^[^\\.].*\\.php$/", $file ) ) {
				require_once UCB_RECOMMEND_LIB_API_DIR . DIRECTORY_SEPARATOR . $file;
				if ( isset( $GLOBALS[UCBRecommend_API_Base::get_name( $file )] ) ) {
					$obj = $GLOBALS[UCBRecommend_API_Base::get_name( $file )];
					if ( method_exists( $obj, 'get_api_name' ) && is_callable( array( $obj, 'get_api_name' ) ) &&
						method_exists( $obj, 'get_capability' ) && is_callable( array( $obj, 'get_capability' ) ) &&
						method_exists( $obj, 'setup' ) && is_callable( array( $obj, 'setup' ) ) &&
						method_exists( $obj, 'is_basic_api' ) && is_callable( array( $obj, 'is_basic_api' ) )
					) {
						$obj->setup();
					}
				}
			}
		}

		foreach ( scandir( UCB_RECOMMEND_API_DIR ) as $file ) {
			if ( preg_match( "/^[^\\.].*\\.php$/", $file ) ) {
				require_once UCB_RECOMMEND_API_DIR . DIRECTORY_SEPARATOR . $file;
				if ( isset( $GLOBALS[UCBRecommend_API_Base::get_name( $file )] ) ) {
					$obj = $GLOBALS[UCBRecommend_API_Base::get_name( $file )];
					if ( method_exists( $obj, 'get_api_name' ) && is_callable( array( $obj, 'get_api_name' ) ) &&
						method_exists( $obj, 'get_capability' ) && is_callable( array( $obj, 'get_capability' ) ) &&
						method_exists( $obj, 'setup' ) && is_callable( array( $obj, 'setup' ) ) &&
						method_exists( $obj, 'is_basic_api' ) && is_callable( array( $obj, 'is_basic_api' ) )
					) {
						if ( $this->defined( 'DOING_AJAX' ) || !is_admin() || in_array( $obj->get_api_name(), $this->use_functions ) || $obj->is_basic_api() ) {
							$obj->setup();
						}
					}
				}
			}
		}

		if ( !$this->defined( 'DOING_AJAX' ) ) {
			global $ucbr_minify;
			if ( is_admin() ) {
				$ucbr_minify->register_script( $this->view( "ajaxurl-admin", false, array(), true ) );
			} else {
				global $ucbr_ajax;
				$ucbr_minify->register_script( $this->view( "ajaxurl", false, array( "ajaxurl" => $ucbr_ajax->get_ajax_url() ), true ) );
			}
		}
	}
}

$GLOBALS['ucbr_api'] = UCBRecommend_API_Loader::get_instance();
