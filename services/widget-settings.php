<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_WidgetSettings extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	protected function __construct()
	{
		$this->register();
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_WidgetSettings();
		}
		return self::$_instance;
	}

	private function register()
	{
		add_filter( $this->get_filter_prefix() . 'data_number', function ( $num, $id ) {
			global $ucbr_option;
			return $ucbr_option->get( 'data_number-' . $id, $num );
		}, 10, 2 );

		add_filter( $this->get_filter_prefix() . 'no_context_mode', function ( $flag, $id ) {
			global $ucbr_option;
			return $ucbr_option->get( 'no_context_mode-' . $id, $flag );
		}, 10, 2 );
	}

	public function get( $id, $name )
	{
		switch ( $name ) {
			case 'data_number':
				return $this->get_data_number( $id );
			case 'no_context_mode':
				return $this->get_no_context_mode( $id );
			default:
				return null;
		}
	}

	public function get_data_number( $id )
	{
		return $this->apply_filters( 'data_number', UCB_RECOMMEND_GET_DATA_NUMBER, $id ) - 0;
	}

	public function get_no_context_mode( $id )
	{
		return $this->apply_filters( 'no_context_mode', UCB_RECOMMEND_NO_CONTEXT_MODE, $id );
	}

	public function set( $id, $name, $value )
	{
		switch ( $name ) {
			case 'data_number':
				$this->set_data_number( $id, (int)$value );
				return true;
			case 'no_context_mode':
				$this->set_no_context_mode( $id, (bool)$value );
				return true;
		}
		return false;
	}

	public function set_data_number( $id, $num )
	{
		global $ucbr_option;
		$ucbr_option->set( 'data_number-' . $id, $num );
	}

	public function set_no_context_mode( $id, $flag )
	{
		global $ucbr_option;
		$ucbr_option->set( 'no_context_mode-' . $id, $flag );
	}

	public function delete_data_number( $id )
	{
		global $ucbr_option;
		$ucbr_option->delete( 'data_number-' . $id );
	}

	public function delete_no_context_mode( $id )
	{
		global $ucbr_option;
		$ucbr_option->delete( 'no_context_mode-' . $id );
	}

	public function delete( $id, $name )
	{
		switch ( $name ) {
			case 'data_number':
				$this->delete_data_number( $id );
				return true;
			case 'no_context_mode':
				$this->delete_no_context_mode( $id );
				return true;
		}
		return false;
	}

	public function delete_all( $id )
	{
		$this->delete_data_number( $id );
		$this->delete_no_context_mode( $id );
	}
}

$GLOBALS['ucbr_widget_settings'] = UCBRecommend_WidgetSettings::get_instance();
