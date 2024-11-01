<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Widget extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Widget();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'widget';
	}

	public function get_method()
	{
		return 'get';
	}

	//	public function need_nonce_check()
	//	{
	//		return true;
	//	}

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

		if ( !isset( $_REQUEST['p'] ) || $_REQUEST['p'] <= 0 ) {
			$_REQUEST['p'] = 0;
		}

		global $ucbr_widget_settings;
		$no_context = $ucbr_widget_settings->get_no_context_mode( $_REQUEST['id'] );
		if ( $no_context ) {
			$post_id = 0;
			$post = null;
		} else {
			$post_id = $_REQUEST['p'];
			$post = null;
			if ( $post_id > 0 ) {
				$post = get_post( $post_id );
				if ( is_null( $post ) ) {
					$post_id = 0;
				}
			}
		}

		if ( isset( $_REQUEST['preview'] ) && $_REQUEST['preview'] ) {
			$preview = true;
		} else {

			global $ucbr_design, $ucbr_deviceinfo;
			$devices = $ucbr_design->get_valid_devices( $_REQUEST['id'] );
			$custom = trim( $ucbr_design->get_custom_valid_device( $_REQUEST['id'] ) );
			$valid = true;
			if ( is_array( $devices ) && count( $devices ) > 0 ) {
				$valid = false;
				foreach ( $devices as $device ) {
					if ( $ucbr_deviceinfo->check_device( $device ) ) {
						$valid = true;
						break;
					}
				}
				if ( !$valid && "" !== $custom ) {
					$valid = $ucbr_deviceinfo->check( $custom );
				}
			} elseif ( "" !== $custom ) {
				$valid = $ucbr_deviceinfo->check( $custom );
			}
			if ( !$valid ) {
				return array(
					'result' => false,
					'message' => 'device check failed',
					'elapsed' => $elapsed( $start )
				);
			}
			$preview = false;
		}

		$exclude = false;
		global $ucbr_deviceinfo;
		if ( !$preview && $ucbr_deviceinfo->is_bot() ) {
			$exclude = true;
		} elseif ( !$preview && $this->apply_filters( "exclude_loggedin_user", UCB_RECOMMEND_EXCLUDE_LOGGEDIN_USER ) ) {
			global $ucbr_user;
			if ( $ucbr_user->loggedin ) {
				$exclude = true;
			}
		}

		$number = $ucbr_widget_settings->get_data_number( $_REQUEST['id'] );
		$post_ids = $ucbr_condition->get_post_ids( $_REQUEST['id'], $post_id );

		global $ucbr_calculate;
		$data = $ucbr_calculate->get_bandits( $_REQUEST['id'], $post_id, $number, $post_ids );

		if ( !$preview && !$exclude ) {
			global $ucbr_data;
			$registered = $ucbr_data->register( $_REQUEST['id'], $post_id, array_map( function ( $d ) {
				return $d['post_id'];
			}, $data ) );
		} else {
			$registered = false;
		}

		global $ucbr_design;
		$html = $ucbr_design->parse_template( $_REQUEST['id'], $post, $post_id, $data, $registered, $preview );

		return array(
			'result' => true,
			'message' => $html,
			'elapsed' => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_Widget::get_instance();
