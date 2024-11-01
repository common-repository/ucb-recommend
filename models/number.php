<?php
namespace UCBRecommendModel;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Number extends UCBRecommend_Model_Base
{

	private static $_instance = null;

	private function __construct()
	{

	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Number();
		}
		return self::$_instance;
	}

	protected function get_table()
	{
		return UCBRecommend_Model_Base::get_slug( __FILE__ );
	}
}

$GLOBALS[UCBRecommend_Model_Base::get_name( __FILE__ )] = UCBRecommend_Number::get_instance();
