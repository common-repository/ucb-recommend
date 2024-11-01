<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_GetTests extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_GetTests();
		}
		return self::$_instance;
	}

	protected function need_nonce_check()
	{
		return true;
	}

	public function get_api_name()
	{
		return "get_tests";
	}

	public function get_method()
	{
		return "get";
	}

	protected function only_admin()
	{
		return true;
	}

	public function get_response()
	{
		$start = microtime( true );
		$elapsed = function ( $start ) {
			return round( microtime( true ) - $start, 6 ) * 1000 . ' ms';
		};

		global $ucbr_test;
		return array(
			"result" => $ucbr_test->get_test_settings(),
			"elapsed" => $elapsed( $start )
		);
	}
}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_GetTests::get_instance();
