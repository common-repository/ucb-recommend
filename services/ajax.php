<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Ajax extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	protected function __construct()
	{
		$this->check_url();
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Ajax();
		}
		return self::$_instance;
	}

	private function check_url()
	{
		if ( $this->defined( 'DOING_AJAX' ) ) {
			return;
		}

		$exploded = explode( '?', home_url( $_SERVER["REQUEST_URI"] ) );
		if ( $exploded[0] !== $this->get_ajax_url( false ) ) {
			return;
		}

		define( 'DOING_AJAX', true );

		if ( empty( $_REQUEST['action'] ) )
			die( '0' );

		add_action( 'wp_loaded', array( $this, 'setup' ) );
	}

	public function setup()
	{
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );

		send_nosniff_header();
		nocache_headers();

		if ( is_user_logged_in() ) {
			do_action( 'wp_ajax_' . $_REQUEST['action'] );
		} else {
			do_action( 'wp_ajax_nopriv_' . $_REQUEST['action'] );
		}

		// Default status
		die( '0' );
	}

	public function get_ajax_url( $admin_ajax = null )
	{
		if ( is_null( $admin_ajax ) ) {
			if ( $this->apply_filters( 'front_admin_ajax', UCB_RECOMMEND_FRONT_ADMIN_AJAX ) ) {
				return admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' );
			}
			return $this->apply_filters( 'ajax_url', UCB_RECOMMEND_PLUGIN_URL . '/' . UCB_RECOMMEND_AJAX_FILE );
		}

		if ( $admin_ajax ) {
			return admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' );
		}
		return $this->apply_filters( 'ajax_url', UCB_RECOMMEND_PLUGIN_URL . '/' . UCB_RECOMMEND_AJAX_FILE );
	}

}

$GLOBALS['ucbr_ajax'] = UCBRecommend_Ajax::get_instance();
