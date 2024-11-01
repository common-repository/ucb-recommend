<?php
namespace UCBRecommendApi;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_GetPreview extends UCBRecommend_API_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_GetPreview();
		}
		return self::$_instance;
	}

	public function get_api_name()
	{
		return 'get_preview';
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

		$strip_slashed = function ( $arr ) use ( &$strip_slashed ) {
			return is_array( $arr ) ?
				array_map( function ( $a ) use ( $strip_slashed ) {
					return $strip_slashed( $a );
				}, $arr ) :
				stripslashes( $arr );
		};
		$_REQUEST = $strip_slashed( $_REQUEST );

		if ( !isset( $_REQUEST['load_widget'] ) ) {
			$_REQUEST['load_widget'] = null;
		}

		if ( !isset( $_REQUEST['loading'] ) ) {
			$_REQUEST['loading'] = null;
		}

		if ( !isset( $_REQUEST['list'] ) ) {
			$_REQUEST['list'] = null;
		}

		if ( !isset( $_REQUEST['item'] ) ) {
			$_REQUEST['item'] = null;
		}

		if ( !isset( $_REQUEST['not_found'] ) ) {
			$_REQUEST['not_found'] = null;
		}

		if ( !isset( $_REQUEST['style'] ) ) {
			$_REQUEST['style'] = null;
		}

		if ( !isset( $_REQUEST['no_thumb'] ) ) {
			$_REQUEST['no_thumb'] = null;
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

		global $ucbr_widget_settings;
		$no_context = $ucbr_widget_settings->get_no_context_mode( $_REQUEST['id'] );
		if ( $no_context ) {
			$context_post_id = 0;
			$context_post = null;
		} else {
			$context_post_id = 0;
			$context_post = new \WP_Post( $this->get_post_object( $context_post_id ) );
		}

		$number = $ucbr_widget_settings->get_data_number( $_REQUEST['id'] );
		$item_posts = array();
		for ( ; --$number >= 0; ) {
			$item_posts [] = new \WP_Post( $this->get_post_object( 0 ) );
		}

		global $ucbr_design;
		try {
			$loading = $ucbr_design->parse_load_widget_template2( $_REQUEST['id'], $_REQUEST['loading'], $_REQUEST['load_widget'] );
			$list = $ucbr_design->parse_template2( $_REQUEST['id'], $context_post, $context_post_id, $item_posts, null, $_REQUEST['list'], $_REQUEST['not_found'], $_REQUEST['item'], $_REQUEST['style'], $_REQUEST['no_thumb'], true );
			$not_found = $ucbr_design->parse_template2( $_REQUEST['id'], $context_post, $context_post_id, array(), null, $_REQUEST['list'], $_REQUEST['not_found'], $_REQUEST['item'], $_REQUEST['style'], $_REQUEST['no_thumb'], true );

			$result = array(
				'loading' => $loading,
				'list' => $list,
				'not_found' => $not_found
			);

			return array(
				'result' => $result,
				'message' => 'accepted',
				'elapsed' => $elapsed( $start )
			);
		} catch ( \Exception $e ) {
			return array(
				'result' => false,
				'message' => $e->getMessage(),
				'elapsed' => $elapsed( $start )
			);
		}
	}

	private function get_post_object( $id )
	{
		$now = current_time( 'mysql' );
		$now_gmt = current_time( 'mysql', 1 );
		$first_post_guid = get_option( 'home' ) . '/?p=' . $id;
		$first_post = __( 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!' );
		return (object)array(
			'ID' => $id,
			'post_author' => 1,
			'post_date' => $now,
			'post_date_gmt' => $now_gmt,
			'post_content' => $first_post,
			'post_excerpt' => '',
			'post_title' => __( 'Hello world!' ),
			/* translators: Default post slug */
			'post_name' => sanitize_title( _x( 'hello-world', 'Default post slug' ) ),
			'post_modified' => $now,
			'post_modified_gmt' => $now_gmt,
			'guid' => $first_post_guid,
			'comment_count' => 1,
			'to_ping' => '',
			'pinged' => '',
			'post_content_filtered' => ''
		);
	}

}

$GLOBALS[UCBRecommend_API_Base::get_name( __FILE__ )] = UCBRecommend_GetPreview::get_instance();
