<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Update extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	protected function __construct()
	{
		$this->register();
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Update();
		}
		return self::$_instance;
	}

	private function register()
	{
		add_action( 'ucbr_updated', function ( $version ) {
			if ( version_compare( $version, '0.2.5', '<' ) ) {
				$this->version_0_2_5();
			}
			if ( version_compare( $version, '0.2.6', '<' ) ) {
				$this->version_0_2_6();
			}
			if ( version_compare( $version, '0.2.8', '<' ) ) {
				$this->version_0_2_8();
			}
			if ( version_compare( $version, '1.0.6', '<' ) ) {
				$this->version_1_0_6();
			}
			if ( version_compare( $version, '1.0.8', '<' ) ) {
				$this->version_1_0_8();
			}
		} );
	}

	private function version_0_2_5()
	{
		global $ucbr_model_test, $ucbr_model_number;
		$ucbr_model_test->clear( false );
		$ucbr_model_number->clear( false );
	}

	private function version_0_2_6()
	{
		global $wpdb, $ucbr_custom_post_type;
		$sql = <<< EOS
			UPDATE $wpdb->posts
			SET post_type = %s
			WHERE
				post_type = %s
EOS;
		$sql = $wpdb->prepare( $sql, array(
			$ucbr_custom_post_type->get_post_type(),
			'ucbr_redirect'
		) );
		$wpdb->query( $sql );
	}

	private function version_0_2_8()
	{
		global $ucbr_model_object, $ucbr_custom_post_type;
		$ucbr_model_object->update(
			array(
				'value' => $ucbr_custom_post_type->get_post_type()
			),
			array(
				array(
					'AND',
					array(
						array( 'value', 'like', '?', 'ucbr_redirect' )
					)
				)
			)
		);
	}

	private function version_1_0_6()
	{
		global $ucbr_option;
		$name = $this->get_filter_prefix() . 'front_admin_ajax';
		if ( "" === $ucbr_option->get( $name ) ) {
			$ucbr_option->set( $name, 'true' );
		}
	}

	private function version_1_0_8()
	{
		global $ucbr_option;
		$name = $this->get_filter_prefix() . 'check_update';
		$ucbr_option->set( $name, 'false' );
	}
}

$GLOBALS['ucbr_update'] = UCBRecommend_Update::get_instance();
