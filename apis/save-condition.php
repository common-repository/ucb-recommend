<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_SaveCondition extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_SaveCondition();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'save_condition';
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

		if ( !isset( $_REQUEST['group'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [group] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['table'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [table] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['column'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [column] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['verb'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [verb] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( empty( $_REQUEST['table'] ) || empty( $_REQUEST['column'] ) || '' === $_REQUEST['verb'] ) {
			global $wpdb;
			$_REQUEST['table'] = $wpdb->posts;
			$_REQUEST['column'] = 'ID';
			$_REQUEST['verb'] = 0;
		}

		global $ucbr_condition;
		$result = $ucbr_condition->save_condition( $_REQUEST['id'], $_REQUEST['group'], $_REQUEST['table'], $_REQUEST['column'], $_REQUEST['verb'] );

		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_SaveCondition::get_instance();
