<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_GetValidDevices extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_GetValidDevices();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'get_valid_devices';
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
		$widget = $ucbr_condition->get_widget( $_REQUEST['id'] );
		if ( !$widget ) {
			return array(
				'result' => false,
				'message' => 'invalid parameter [id]',
				'elapsed' => $elapsed( $start )
			);
		}

		global $ucbr_design;
		$devices = $ucbr_design->get_valid_devices( $_REQUEST['id'] );
		$custom = $ucbr_design->get_custom_valid_device( $_REQUEST['id'] );
		$result = array( 'devices' => $devices, 'custom' => $custom );

		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_GetValidDevices::get_instance();
