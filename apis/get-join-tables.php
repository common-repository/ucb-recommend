<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_GetJoinTables extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_GetJoinTables();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'get_join_tables';
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

		if ( !isset( $_REQUEST['id'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [id] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		global $ucbr_condition;
		$result = $ucbr_condition->get_join_tables( $_REQUEST['id'] );

		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_GetJoinTables::get_instance();
