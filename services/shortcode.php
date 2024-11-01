<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Shortcode extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	private $id_hash = array();

	protected function __construct()
	{
		$this->register();
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Shortcode();
		}
		return self::$_instance;
	}

	public function get_widget_shortcode()
	{
		return $this->apply_filters( 'widget_shortcode', UCB_RECOMMEND_WIDGET_SHORTCODE );
	}

	private function register()
	{
		add_shortcode( $this->get_widget_shortcode(), function ( $atts ) {
			$id = null;
			$lazy = 1;
			$fadeIn = 400;
			extract(
				shortcode_atts(
					array(
						'id' => -1,
						'lazy' => 1,
						'fadeIn' => 400
					), $atts
				)
			);

			return $this->widget( $id - 0, $lazy, $fadeIn );
		} );
	}

	private function widget( $id, $lazy, $fadeIn )
	{
		if ( $id <= 0 ) {
			return '';
		}

		if ( count( $this->id_hash ) <= 0 ) {
			global $ucbr_minify;
			$ucbr_minify->register_script( $this->view( 'widget-script', false, array( 'post_id' => get_the_ID(), 'lazy' => $lazy, 'fadeIn' => $fadeIn ) ) );
			if ( $lazy ) {
				$ucbr_minify->register_js_file( UCB_RECOMMEND_JS_DIR . DIRECTORY_SEPARATOR . 'jquery.inview.min.js' );
			}
		}

		global $ucbr_design;
		if ( !array_key_exists( $id, $this->id_hash ) ) {
			$this->id_hash[$id] = true;

			global $ucbr_minify;
			$ucbr_minify->register_css( $ucbr_design->parse_style_template( $id ), true );
		}

		return $ucbr_design->parse_load_widget_template( $id );
	}

}

$GLOBALS['ucbr_shortcode'] = UCBRecommend_Shortcode::get_instance();
