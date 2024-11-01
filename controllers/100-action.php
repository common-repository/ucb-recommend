<?php
namespace UCBRecommendController;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Action extends UCBRecommend_Controller_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Action();
		}
		return self::$_instance;
	}

	public function get_page_title()
	{
		return __( 'Action', UCB_RECOMMEND_TEXT_DOMAIN );
	}

	public function get_menu_name()
	{
		return __( 'Action', UCB_RECOMMEND_TEXT_DOMAIN );
	}

	private function get_actions()
	{
		return $this->apply_filters( 'action_list', array(
			'delete-log' => array(
				'description' => "Delete log.",
				'action' => 'delete_log',
				'confirm' => true
			),
			'reset-settings' => array(
				'description' => "Initialize all settings, widgets and data.",
				'action' => 'initialize',
				'confirm' => true
			),
		) );
	}

	public function setup()
	{
		$actions = $this->get_actions();
		if ( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' && isset( $_REQUEST['ucbr-action'] ) && array_key_exists( $_REQUEST['ucbr-action'], $actions ) && isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'ucbr-action' ) ) {
			$action = $actions[$_REQUEST['ucbr-action']];
			if ( is_callable( array( $this, $action['action'] ) ) ) {
				$this->{$action['action']}();
			}
			$this->do_action( 'did_action', $_REQUEST['ucbr-action'] );
		}
	}

	public function load()
	{
		$data = array();
		foreach ( $this->get_actions() as $k => $v ) {
			$data[$k] = $v;
			$data[$k]['description'] = implode( '<br>', array_map( function ( $d ) {
				return __( $d, UCB_RECOMMEND_TEXT_DOMAIN );
			}, explode( "\n", $v['description'] ) ) );
		}
		$this->view( 'action', true, array( 'data' => $data, 'nonce' => wp_create_nonce( 'ucbr-action' ) ) );
	}

	private function delete_log()
	{
		if ( file_exists( UCB_RECOMMEND_LOG_FILE ) ) {
			unlink( UCB_RECOMMEND_LOG_FILE );
		}
		$this->message( __( 'Done.', UCB_RECOMMEND_TEXT_DOMAIN ) );
	}

	private function initialize()
	{
		global $ucbr_db, $ucbr_option, $ucbr_post, $ucbr_user;
		$ucbr_db->uninstall();
		$ucbr_option->uninstall();
		$ucbr_post->uninstall();
		$ucbr_user->uninstall();

		wp_clear_scheduled_hook( 'ucbr_clear_event' );
		wp_clear_scheduled_hook( 'ucbr_clear_hook' );
		$this->message( __( 'Done.', UCB_RECOMMEND_TEXT_DOMAIN ) );
	}

}

$GLOBALS[UCBRecommend_Controller_Base::get_name( __FILE__ )] = UCBRecommend_Action::get_instance();

