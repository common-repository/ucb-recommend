<?php
namespace UCBRecommendController;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

abstract class UCBRecommend_Controller_Base extends \UCBRecommendBase\UCBRecommend_Base_Class
{

	abstract public function get_page_title();

	abstract public function get_menu_name();

	public function get_capability()
	{
		return null;
	}

	public function setup()
	{

	}

	abstract public function load();

	public static function get_prefix()
	{
		return "ucbr_controller_";
	}

	public static function get_slug( $file )
	{
		return preg_replace( "/^\\d+\\-(.+)\\.php$/", "$1", basename( $file ) );
	}

	public static function get_name( $file )
	{
		return self::get_prefix() . self::get_slug( $file );
	}

	public static function get_page( $file )
	{
		return "ucbr-" . self::get_slug( $file );
	}

}
