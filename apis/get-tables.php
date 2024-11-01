<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_GetTables extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_GetTables();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'get_tables';
	}

	public function get_method()
	{
		return 'get';
	}

	public function only_admin()
	{
		return true;
	}

	public function get_response()
	{
		$start = microtime( true );
		$elapsed = function ( $start ) {
			return round( microtime( true ) - $start, 6 ) * 1000 . ' ms';
		};

		global $ucbr_condition;
		$result = $ucbr_condition->get_tables( );

		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_GetTables::get_instance();
