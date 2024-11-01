<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_GetDesignTemplates extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_GetDesignTemplates();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'get_design_templates';
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

		global $ucbr_design;
		$load_widget_template = $ucbr_design->get_load_widget_template( $_REQUEST['id'] );
		$list_template = $ucbr_design->get_list_template( $_REQUEST['id'] );
		$item_template = $ucbr_design->get_item_template( $_REQUEST['id'] );
		$not_found_template = $ucbr_design->get_not_found_template( $_REQUEST['id'] );
		$no_thumb = $ucbr_design->get_no_thumb_img( $_REQUEST['id'] );
		$loading = $ucbr_design->get_loading_img( $_REQUEST['id'] );
		$style_template = $ucbr_design->get_style_template( $_REQUEST['id'] );

		$result = array(
			'load_widget' => $load_widget_template,
			'list' => $list_template,
			'item' => $item_template,
			'not_found' => $not_found_template,
			'no_thumb' => $no_thumb,
			'loading' => $loading,
			'style' => $style_template
		);

		return array(
			"result" => $result,
			"message" => "accepted",
			"elapsed" => $elapsed( $start )
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_GetDesignTemplates::get_instance();
