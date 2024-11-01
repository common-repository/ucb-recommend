<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_GetHistories extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_GetHistories();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'get_histories';
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

		global $ucbr_model_widget;
		$widget_id = $ucbr_model_widget->get_value( $widget, 'uuid' );

		global $ucbr_db, $ucbr_widget_settings;;
		$table = $ucbr_db->get_table( 'test' );
		$sql = <<< EOS
SELECT *
FROM $table
WHERE
	deleted_at IS NOT NULL AND
	widget_id like ?
ORDER BY
	updated_at DESC
LIMIT ?
EOS;
		$bind = $ucbr_db->init_bind( array( 's', 'i' ), array( $widget_id, $this->apply_filters( 'get_histories_number', 50 ) ) );
		$data = $ucbr_db->fetch_all( $sql, $bind, 'test', __FILE__, __LINE__ );

		$sql = <<< EOS
SELECT COUNT(*) as num
FROM $table
WHERE
	widget_id like ?
EOS;
		$bind = $ucbr_db->init_bind( array( 's' ), array( $widget_id ) );
		$total = $ucbr_db->fetch( $sql, $bind, 'test', __FILE__, __LINE__ );
		$total_num = $total->num;
		$number = $ucbr_widget_settings->get_data_number( $_REQUEST['id'] );
		$total = __( 'Total shown item number', UCB_RECOMMEND_TEXT_DOMAIN ) . ': ';
		if ( $number <= 0 ) {
			$total .= '0';
		} elseif ( $number > 1 ) {
			$t = (int)ceil( 1.0 * $total_num / $number );
			$total .= sprintf( '%d (≒%dx%d)', $total_num, $number, $t );
		} else {
			$total .= sprintf( '%d', $total_num );
		}

		$expire = $this->apply_filters( 'data_expire', UCB_RECOMMEND_DATA_EXPIRE );
		$diff = human_time_diff( 0, $expire );
		$diff = sprintf( __( 'Data for about %s', UCB_RECOMMEND_TEXT_DOMAIN ), $diff );

		$no_context = $ucbr_widget_settings->get_no_context_mode( $_REQUEST['id'] );

		$result = array(
			'total' => $total,
			'diff' => $diff,
			'data' => $data,
			'no_context' => $no_context
		);
		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_GetHistories::get_instance();
