<?php
namespace UCBRecommendController;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Widget extends UCBRecommend_Controller_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Widget();
		}
		return self::$_instance;
	}

	public function get_page_title()
	{
		return __( "Widget", UCB_RECOMMEND_TEXT_DOMAIN );
	}

	public function get_menu_name()
	{
		return __( "Widget", UCB_RECOMMEND_TEXT_DOMAIN );
	}

	public function load()
	{
		$loading_image = $this->apply_filters( "loading_image", UCB_RECOMMEND_LIB_IMG_DIR . DIRECTORY_SEPARATOR . UCB_RECOMMEND_LOADING_IMAGE, -1 );
		$loading_image = $this->dir2path( $loading_image );

		$back_file = $this->apply_filters( "back_image", UCB_RECOMMEND_LIB_IMG_DIR . DIRECTORY_SEPARATOR . 'back.png' );
		$back_file = $this->dir2path( $back_file );

		global $ucbr_minify, $ucbr_shortcode;
		$ucbr_minify->register_script( $this->view( 'modal-script', false, array(), true ) );
		$ucbr_minify->register_script( $this->view( 'controls', false, array(), true ) );
		$ucbr_minify->register_script( $this->view( 'widget-settings-script', false, array(
			'loading_image' => $loading_image,
			'shortcode' => $ucbr_shortcode->get_widget_shortcode(),
			'condition_test_number' => $this->apply_filters( 'condition_test_number', UCB_RECOMMEND_CONDITION_TEST_NUMBER )
		) ) );
		$ucbr_minify->register_js_file( UCB_RECOMMEND_JS_DIR . DIRECTORY_SEPARATOR . 'jquery.autosize.min.js' );
		$ucbr_minify->register_css( $this->view( 'modal-style', false, array(
			'loading_image' => $loading_image,
			"back_file" => $back_file
		), true ) );

		global $ucbr_api;
		$ucbr_api->register_use_function( 'get-tables' );
		$ucbr_api->register_use_function( 'get-columns' );
		$ucbr_api->register_use_function( 'get-types' );
		$ucbr_api->register_use_function( 'get-verbs' );
		$ucbr_api->register_use_function( 'get-join-types' );
		$ucbr_api->register_use_function( 'get-condition-set' );
		$ucbr_api->register_use_function( 'get-design-templates' );
		$ucbr_api->register_use_function( 'get-widget-setting' );
		$ucbr_api->register_use_function( 'get-devices' );
		$ucbr_api->register_use_function( 'get-valid-devices' );

		$ucbr_api->register_use_function( 'get-join-tables' );
		$ucbr_api->register_use_function( 'get-widgets' );
		$ucbr_api->register_use_function( 'get-condition-groups' );
		$ucbr_api->register_use_function( 'get-conditions' );
		$ucbr_api->register_use_function( 'get-objects' );
		$ucbr_api->register_use_function( 'get-post-ids' );
		$ucbr_api->register_use_function( 'get-posts' );
		$ucbr_api->register_use_function( 'get-bandits' );
		$ucbr_api->register_use_function( 'get-preview' );
		$ucbr_api->register_use_function( 'get-histories' );

		$ucbr_api->register_use_function( 'save-join-table' );
		$ucbr_api->register_use_function( 'save-widget' );
		$ucbr_api->register_use_function( 'save-condition-group' );
		$ucbr_api->register_use_function( 'save-condition' );
		$ucbr_api->register_use_function( 'save-objects' );
		$ucbr_api->register_use_function( 'add-condition-set' );
		$ucbr_api->register_use_function( 'save-design-templates' );
		$ucbr_api->register_use_function( 'save-widget-setting' );
		$ucbr_api->register_use_function( 'save-valid-devices' );

		$ucbr_api->register_use_function( 'delete-join-table' );
		$ucbr_api->register_use_function( 'delete-widget' );
		$ucbr_api->register_use_function( 'delete-condition-group' );
		$ucbr_api->register_use_function( 'delete-condition' );
		$ucbr_api->register_use_function( 'delete-objects' );

		$ucbr_api->register_use_function( 'widget' );

		$this->view( "widget-settings" );
	}
}

$GLOBALS[UCBRecommend_Controller_Base::get_name( __FILE__ )] = UCBRecommend_Widget::get_instance();

