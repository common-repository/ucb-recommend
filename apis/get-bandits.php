<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_GetBandits extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_GetBandits();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'get_bandits';
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

		if ( !isset( $_REQUEST['p'] ) ) {
			$post_id = null;
		} else {
			$post_id = $_REQUEST['p'] - 0;
			//			$post = get_post( $post_id );
			//			if ( !$post ) {
			//				$post_id = null;
			//			}
		}

		if ( !isset( $_REQUEST['n'] ) ) {
			$number = $this->apply_filters( 'condition_test_number', UCB_RECOMMEND_CONDITION_TEST_NUMBER );
		} else {
			$number = $_REQUEST['n'] - 0;
			if ( $number <= 0 ) {
				$number = 1;
			}
			if ( $number > 100 ) {
				$number = 100;
			}
		}

		global $ucbr_condition;
		$post_ids = $ucbr_condition->get_post_ids( $_REQUEST['id'], $post_id );

		global $ucbr_calculate;
		$result = $ucbr_calculate->get_bandits( $_REQUEST['id'], $post_id, $number, $post_ids );

		$std_dev = $this->apply_filters( 'std_dev', UCB_RECOMMEND_BANDIT_RANDOM_STD_DEV );

		$result = array_map( function ( $d ) {
			if ( PHP_INT_MAX === $d['bandit'] ) {
				$d['bandit'] = 'inf';
			}
			$d['post_title'] = get_the_title($d['post_id']);
			return $d;
		}, $result );

		if ($post_id > 0) {
			$title = get_the_title($post_id);
		} else {
			$title = '';
		}

		$result = array(
			'list' => $result,
			'title' => $title,
			'rand' => $std_dev > 0
		);

		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_GetBandits::get_instance();
