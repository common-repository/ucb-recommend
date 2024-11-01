<?php
namespace UCBRecommendController;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Setting extends UCBRecommend_Controller_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Setting();
		}
		return self::$_instance;
	}

	public function get_page_title()
	{
		return __( "Dashboard", UCB_RECOMMEND_TEXT_DOMAIN );
	}

	public function get_menu_name()
	{
		return __( "Dashboard", UCB_RECOMMEND_TEXT_DOMAIN );
	}

	public function setup()
	{
		if ( strtolower( $_SERVER["REQUEST_METHOD"] ) == "post" && isset( $_REQUEST["nonce"] ) && wp_verify_nonce( $_REQUEST["nonce"], "ucbr-setting" ) ) {
			global $ucbr_option;
			$settings = $this->get_settings();
			foreach ( $settings as $k => $v ) {
				$ucbr_option->set_post( $v["name"], false );
			}
			$ucbr_option->save();
			$this->message( __( "Options saved.", UCB_RECOMMEND_TEXT_DOMAIN ) );
		}
	}

	public function load()
	{
		$settings = $this->get_settings();

		foreach ( $settings as $k => $v ) {
			if ( !isset( $v["key"] ) ) {
				unset( $settings[$k] );
				continue;
			}
			if ( !isset( $v["label"] ) ) {
				$settings[$k]["label"] = $v["key"];
			}
			if ( !isset( $v["placeholder"] ) ) {
				$settings[$k]["placeholder"] = "";
			}
			$tail = " [default = " . $this->get_expression( $settings[$k]['default'], $settings[$k]['type'] );
			if ( isset( $settings[$k]["min"] ) ) {
				$tail .= ", min = " . $this->get_expression( $settings[$k]['min'], $settings[$k]['type'] );
			}
			if ( isset( $settings[$k]["max"] ) ) {
				$tail .= ", max = " . $this->get_expression( $settings[$k]['max'], $settings[$k]['type'] );
			}
			$tail .= "]";
			$settings[$k]["label"] = __( $settings[$k]["label"], UCB_RECOMMEND_TEXT_DOMAIN ) . $tail;
			$settings[$k]["used"] = $this->get_expression( $this->apply_filters( $settings[$k]["key"], $settings[$k]["default"] ), $settings[$k]['type'] );
			$settings[$settings[$k]["key"]] = $settings[$k];
			unset( $settings[$k] );
		}

		$this->view( "setting", true, array( "items" => $settings, "nonce" => wp_create_nonce( "ucbr-setting" ) ) );
	}
}

$GLOBALS[UCBRecommend_Controller_Base::get_name( __FILE__ )] = UCBRecommend_Setting::get_instance();

