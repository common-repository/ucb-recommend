<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_AddConditionSet extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_AddConditionSet();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'add_condition_set';
	}

	public function get_method()
	{
		return 'post';
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

		if ( !isset( $_REQUEST['w'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [w] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['s'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [s] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		global $ucbr_condition;
		$result = $ucbr_condition->add_condition_set( $_REQUEST['w'], $_REQUEST['s'] );

		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_AddConditionSet::get_instance();
