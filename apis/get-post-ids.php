<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_GetPostIds extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_GetPostIds();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'get_post_ids';
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

		global $ucbr_condition;
		$result = $ucbr_condition->get_post_ids( $_REQUEST['id'], $post_id );

		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_GetPostIds::get_instance();
