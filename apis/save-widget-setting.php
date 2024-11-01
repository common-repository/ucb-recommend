<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_SaveWidgetSetting extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_SaveWidgetSetting();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'save_widget_setting';
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

		if ( !isset( $_REQUEST['n'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [n] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['v'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [v] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['init'] ) ) {
			$_REQUEST['init'] = false;
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

		global $ucbr_widget_settings;
		if ( $_REQUEST['init'] ) {
			$result = $ucbr_widget_settings->delete( $_REQUEST['id'], $_REQUEST['n'] );
		} else {
			$result = $ucbr_widget_settings->set( $_REQUEST['id'], $_REQUEST['n'], $_REQUEST['v'] );
		}

		if ( !$result ) {
			return array(
				'result' => false,
				'message' => 'invalid parameter [n]',
				'elapsed' => $elapsed( $start )
			);
		}

		return array(
			"result" => true,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_SaveWidgetSetting::get_instance();
