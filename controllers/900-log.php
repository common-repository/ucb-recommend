<?php
namespace UCBRecommendController;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Log extends UCBRecommend_Controller_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Log();
		}
		return self::$_instance;
	}

	public function get_page_title()
	{
		return __( "Log", UCB_RECOMMEND_TEXT_DOMAIN );
	}

	public function get_menu_name()
	{
		return __( "Log", UCB_RECOMMEND_TEXT_DOMAIN );
	}

	public function load()
	{
		$logfile = UCB_RECOMMEND_LOG_FILE;
		$message = array();
		if ( !file_exists( $logfile ) ) {
			$date = array();
		} else {
			$log = @file_get_contents( $logfile );
			$data = preg_split( '#\[(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})\]#', $log, -1, PREG_SPLIT_DELIM_CAPTURE );
			if ( count( $data ) > 0 ) {
				array_shift( $data );
				$date = array_map( 'current', array_chunk( $data, 2 ) );
				$message = array_map( 'current', array_chunk( array_slice( $data, 1 ), 2 ) );
				$message = array_map( 'esc_html', $message );
			} else {
				$date = array();
			}
		}
		$this->view( "log", true, array(
			"date" => $date,
			"message" => $message,
			"number" => $this->apply_filters( "display_log_number", UCB_RECOMMEND_DISPLAY_LOG_NUMBER )
		) );
	}
}

$GLOBALS[UCBRecommend_Controller_Base::get_name( __FILE__ )] = UCBRecommend_Log::get_instance();

