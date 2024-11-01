<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_SaveJoinTable extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_SaveJoinTable();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'save_join_table';
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

		if ( !isset( $_REQUEST['widget'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [widget] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['order'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [order] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['type'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [type] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['table1'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [table1] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['column1'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [column1] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['table2'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [table2] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['column2'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [column2] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( '' === $_REQUEST['order'] ) {
			$_REQUEST['order'] = 0;
		}
		if ( '' === $_REQUEST['type'] ) {
			$_REQUEST['type'] = 0;
		}
		if ( empty( $_REQUEST['table1'] ) || empty( $_REQUEST['column1'] ) || empty( $_REQUEST['table2'] ) || empty($_REQUEST['column2']) ) {
			global $wpdb;
			$_REQUEST['table1'] = $wpdb->posts;
			$_REQUEST['column1'] = 'ID';
			$_REQUEST['table2'] = $wpdb->posts;
			$_REQUEST['column2'] = 'ID';
		}

		global $ucbr_condition;
		$result = $ucbr_condition->save_join_table( $_REQUEST['id'], $_REQUEST['widget'], $_REQUEST['order'], $_REQUEST['type'], $_REQUEST['table1'], $_REQUEST['column1'], $_REQUEST['table2'], $_REQUEST['column2'] );

		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_SaveJoinTable::get_instance();
