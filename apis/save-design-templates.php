<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_SaveDesignTemplate extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_SaveDesignTemplate();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'save_design_templates';
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

		global $ucbr_condition;
		$widget = $ucbr_condition->get_widget( $_REQUEST['id'] );
		if ( !$widget ) {
			return array(
				'result' => false,
				'message' => 'invalid parameter [id]',
				'elapsed' => $elapsed( $start )
			);
		}

		if ( !isset( $_REQUEST['init'] ) ) {
			$_REQUEST['init'] = false;
		}

		if ( !isset( $_REQUEST['load_widget'] ) ) {
			$_REQUEST['load_widget'] = false;
		}

		if ( !isset( $_REQUEST['list'] ) ) {
			$_REQUEST['list'] = false;
		}

		if ( !isset( $_REQUEST['item'] ) ) {
			$_REQUEST['item'] = false;
		}

		if ( !isset( $_REQUEST['not_found'] ) ) {
			$_REQUEST['not_found'] = false;
		}

		if ( !isset( $_REQUEST['no_thumb'] ) ) {
			$_REQUEST['no_thumb'] = false;
		}

		if ( !isset( $_REQUEST['loading'] ) ) {
			$_REQUEST['loading'] = false;
		}

		if ( !isset( $_REQUEST['style'] ) ) {
			$_REQUEST['style'] = false;
		}

		global $ucbr_design;
		if ( false !== $_REQUEST['load_widget'] ) {
			if ( $_REQUEST['init'] ) {
				$ucbr_design->delete_load_widget_template( $_REQUEST['id'], $_REQUEST['load_widget'] );
			} else {
				$ucbr_design->set_load_widget_template( $_REQUEST['id'], $_REQUEST['load_widget'] );
			}
		}
		if ( false !== $_REQUEST['list'] ) {
			if ( $_REQUEST['init'] ) {
				$ucbr_design->delete_list_template( $_REQUEST['id'], $_REQUEST['list'] );
			} else {
				$ucbr_design->set_list_template( $_REQUEST['id'], $_REQUEST['list'] );
			}
		}
		if ( false !== $_REQUEST['item'] ) {
			if ( $_REQUEST['init'] ) {
				$ucbr_design->delete_item_template( $_REQUEST['id'], $_REQUEST['item'] );
			} else {
				$ucbr_design->set_item_template( $_REQUEST['id'], $_REQUEST['item'] );
			}
		}
		if ( false !== $_REQUEST['not_found'] ) {
			if ( $_REQUEST['init'] ) {
				$ucbr_design->delete_not_found_template( $_REQUEST['id'], $_REQUEST['not_found'] );
			} else {
				$ucbr_design->set_not_found_template( $_REQUEST['id'], $_REQUEST['not_found'] );
			}
		}
		if ( false !== $_REQUEST['no_thumb'] ) {
			if ( $_REQUEST['init'] ) {
				$ucbr_design->delete_no_thumb_img( $_REQUEST['id'], $_REQUEST['no_thumb'] );
			} else {
				$ucbr_design->set_no_thumb_img( $_REQUEST['id'], $_REQUEST['no_thumb'] );
			}
		}
		if ( false !== $_REQUEST['loading'] ) {
			if ( $_REQUEST['init'] ) {
				$ucbr_design->delete_loading_img( $_REQUEST['id'], $_REQUEST['loading'] );
			} else {
				$ucbr_design->set_loading_img( $_REQUEST['id'], $_REQUEST['loading'] );
			}
		}
		if ( false !== $_REQUEST['style'] ) {
			if ( $_REQUEST['init'] ) {
				$ucbr_design->delete_style_template( $_REQUEST['id'], $_REQUEST['style'] );
			} else {
				$ucbr_design->set_style_template( $_REQUEST['id'], $_REQUEST['style'] );
			}
		}

		return array(
			"result" => true,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_SaveDesignTemplate::get_instance();
