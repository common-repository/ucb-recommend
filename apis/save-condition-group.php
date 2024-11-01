<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_SaveConditionGroup extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_SaveConditionGroup();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'save_condition_group';
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

		if ( !isset( $_REQUEST['id'] ) ) {
			$_REQUEST['id'] = false;
		}

		if ( !isset( $_REQUEST['w'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [w] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['n'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [n] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		global $ucbr_condition;
		$result = $ucbr_condition->save_condition_group( $_REQUEST['id'], $_REQUEST['w'], $_REQUEST['n'] );

		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_SaveConditionGroup::get_instance();
