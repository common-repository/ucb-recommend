<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_GetResults extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_GetResults();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return "reflect_results";
	}

	public function get_method()
	{
		return "post";
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

		if ( !isset( $_REQUEST["r"] ) || $_REQUEST["r"] <= 0 ) {
			return array(
				"result" => false,
				"message" => "parameter [r] is not set",
				"elapsed" => $elapsed( $start )
			);
		}

		global $ucbr_test;
		$result = $ucbr_test->reflect_result( $_REQUEST['r'] );

		return array(
			"result" => $result,
			"elapsed" => $elapsed( $start )
		);
	}
}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_GetResults::get_instance();
