<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Clear extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	protected function __construct()
	{
		if ( !wp_next_scheduled( 'ucbr_clear_event' ) ) {
			wp_schedule_single_event( time() + $this->apply_filters( 'clear_interval', UCB_RECOMMEND_CLEAR_INTERVAL ), 'ucbr_clear_event' );
		}
		add_action( 'ucbr_clear_event', function () {
			$this->check_progress();
		} );
		add_action( 'ucbr_clear_hook', function () {
			$this->execute();
		} );

		if ( $this->apply_filters( 'clear_log', UCB_RECOMMEND_CLEAR_LOG ) ) {
			add_action( 'ucbr_start_clear_process', function () {
				$this->log( 'start clear' );
			} );
			add_action( 'ucbr_end_clear_process', function ( $start ) {
				$elapsed = ( microtime( true ) - $start ) * 1000;
				$this->log( 'end clear [elapsed time: ' . $elapsed . ' ms]' );
			} );
		}
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Clear();
		}
		return self::$_instance;
	}

	public function clear_event()
	{
		wp_clear_scheduled_hook( 'ucbr_clear_event' );
	}

	private function check_progress()
	{
		if ( wp_next_scheduled( 'ucbr_clear_hook' ) ) {
			return;
		}
		wp_schedule_single_event( time(), 'ucbr_clear_hook' );
	}

	private function execute()
	{
		$start = microtime( true );
		$this->do_action( 'start_clear_process', $start );

		delete_transient( 'doing_cron' );
		set_time_limit( 0 );

		$this->clear();

		$this->do_action( 'end_clear_process', $start );
	}

	private function clear()
	{
		$expire = time() - $this->apply_filters( 'data_expire', UCB_RECOMMEND_DATA_EXPIRE );
		$expire = date( 'Y-m-d H:i:s', $expire );

		global $ucbr_model_test;
		$ucbr_model_test->clear(
			array(
				array(
					'AND',
					array(
						array( 'updated_at', '<', '?', $expire )
					)
				)
			)
		);
	}
}

$GLOBALS['ucbr_clear'] = UCBRecommend_Clear::get_instance();
