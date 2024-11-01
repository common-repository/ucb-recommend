<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_SaveValidDevices extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_SaveValidDevices();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'save_valid_devices';
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
			return array(
				'result' => false,
				'message' => 'parameter [id] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['d'] ) ) {
			$_REQUEST['d'] = array();
		}

		if ( !isset( $_REQUEST['c'] ) ) {
			$_REQUEST['c'] = '';
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

		global $ucbr_design, $ucbr_deviceinfo;
		$devices = $_REQUEST['d'];
		if ( !is_array( $devices ) ) {
			$devices = array();
		}
		$devices = array_filter( $devices, function ( $d ) use ( $ucbr_deviceinfo ) {
			return $ucbr_deviceinfo->get_device( $d ) !== null;
		} );
		$ucbr_design->set_valid_devices( $_REQUEST['id'], $devices );
		$ucbr_design->set_custom_valid_device( $_REQUEST['id'], $_REQUEST['c'] );

		return array(
			"result" => true,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_SaveValidDevices::get_instance();
