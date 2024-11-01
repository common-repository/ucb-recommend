<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Access extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	protected function __construct()
	{
		$this->check_url();
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Access();
		}
		return self::$_instance;
	}

	private function check_url()
	{
		$exploded = explode( '?', home_url( $_SERVER["REQUEST_URI"] ) );
		if ( $exploded[0] !== $this->get_test_base_url() ) {
			return;
		}

		add_action( 'init', function () {

			$exclude = false;
			global $ucbr_deviceinfo;
			if ( $ucbr_deviceinfo->is_bot() ) {
				$exclude = true;
			} elseif ( $this->apply_filters( "exclude_loggedin_user", UCB_RECOMMEND_EXCLUDE_LOGGEDIN_USER ) ) {
				global $ucbr_user;
				if ( $ucbr_user->loggedin ) {
					$exclude = true;
				}
			}

			if ( !isset( $_REQUEST['key'] ) || !isset( $_REQUEST['id'] ) || !isset( $_REQUEST['n'] ) || !isset( $_REQUEST['h'] ) ) {
				return;
			}

			$key = $_REQUEST['key'];
			$id = $_REQUEST['id'];
			$n = $_REQUEST['n'];
			$h = $_REQUEST['h'];

			if ( !$this->check_hash( $key, $id, $n, $h ) ) {
				return;
			}

			global $ucbr_custom_post_type;
			$url = $ucbr_custom_post_type->get_redirect_url( $id, $n );

			if ( !$exclude && $key ) {
				global $ucbr_data;
				$ucbr_data->update( $key );
			}
			wp_redirect( $url );
			exit;
		} );
	}

	private function get_test_base_url()
	{
		return $this->apply_filters( 'test_url', UCB_RECOMMEND_PLUGIN_URL . '/' . UCB_RECOMMEND_TEST_FILE );
	}

	public function get_test_url( $key, $post_id, $index )
	{
		return add_query_arg(
			array(
				'key' => $key,
				'id' => $post_id,
				'n' => $index,
				'h' => $this->make_hash( $key, $post_id, $index )
			),
			$this->get_test_base_url()
		);
	}

	private function make_hash( $key, $post_id, $index )
	{
		$data = $key . '|' . $post_id . '|' . $index;
		$hash = $this->get_hash( $data );
		return substr( $hash, 8, 6 );
	}

	private function check_hash( $key, $post_id, $index, $hash )
	{
		$data = $key . '|' . $post_id . '|' . $index;
		$check = $this->get_hash( $data );
		$check = substr( $check, 8, 6 );
		return $check === $hash;
	}

}

$GLOBALS['ucbr_access'] = UCBRecommend_Access::get_instance();
