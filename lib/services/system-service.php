<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_System extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	protected function __construct()
	{
		$this->initialize();
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_System();
		}
		return self::$_instance;
	}

	private function initialize()
	{
//		load_plugin_textdomain( UCB_RECOMMEND_TEXT_DOMAIN, false, UCB_RECOMMEND_PLUGIN_DIR_NAME . 'languages' );

		if ( $this->apply_filters( "check_update", UCB_RECOMMEND_CHECK_UPDATE ) ) {
			if ( !class_exists( '\PucFactory' ) ) {
				require_once UCB_RECOMMEND_LIB_LIBRARY_DIR . DIRECTORY_SEPARATOR . 'plugin-update-checker' . DIRECTORY_SEPARATOR . 'plugin-update-checker.php';
			}
			\PucFactory::buildUpdateChecker(
				UCB_RECOMMEND_UPDATE_INFO_FILE_URL,
				UCB_RECOMMEND_PLUGIN_FILE_NAME,
				UCB_RECOMMEND_PLUGIN_DIR_NAME
			);
		}

		add_action( 'init', function () {
			$this->check_updated();
		} );
	}

	private function check_updated()
	{
		global $ucbr_option;
		$version = $ucbr_option->get( 'version', -1 );
		if ( version_compare( $version, UCB_RECOMMEND_PLUGIN_VERSION, '<' ) ) {
			$ucbr_option->set( 'version', UCB_RECOMMEND_PLUGIN_VERSION );
			$this->do_action( 'updated', $version );
		}
	}
}

$GLOBALS['ucbr_system'] = UCBRecommend_System::get_instance();
