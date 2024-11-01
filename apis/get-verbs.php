<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_GetVerbs extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_GetVerbs();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'get_verbs';
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

		if ( !isset( $_REQUEST['t'] ) ) {
			return array(
				'result' => false,
				'message' => 'parameter [t] is not set',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['c'] ) ) {
			$_REQUEST['c'] = false;
		} elseif ( 'false' === strtolower( $_REQUEST['c'] ) ) {
			$_REQUEST['c'] = false;
		}

		global $ucbr_condition;
		if ( $_REQUEST['c'] ) {
			$type = $ucbr_condition->convert_mysql_column_type( $_REQUEST['t'] );
		} else {
			$type = $_REQUEST['t'];
		}
		$tmp = $ucbr_condition->get_verbs( $type );
		$verbs = array();
		foreach ( $tmp as $k => $v ) {
			$k = str_replace( '[?]', '...', str_replace( '%%', '%', $k ) );
			$verbs[] = array( $k, $v );
		}
		$result = array('type' => $type, 'verbs' => $verbs);

		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_GetVerbs::get_instance();
