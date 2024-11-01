<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Minify extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	private $script            = "";
	private $has_output_script = false;
	private $css               = "";
	private $end_footer        = false;

	protected function __construct()
	{
		$this->initialize();
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Minify();
		}
		return self::$_instance;
	}

	private function initialize()
	{
		if ( is_admin() ) {
			add_action( 'admin_print_footer_scripts', function () {
				$this->output_js();
			} );
			add_action( 'admin_head', function () {
				$this->output_css();
			} );
			add_action( 'admin_footer', function () {
				$this->output_css();
				$this->end_footer = true;
			} );
		} else {
			add_action( 'wp_print_footer_scripts', function () {
				$this->output_js();
			} );
			add_action( 'wp_print_styles', function () {
				$this->output_css();
			} );
			add_action( 'wp_print_footer_scripts', function () {
				$this->output_css();
				$this->end_footer = true;
			}, 9 );
		}
	}

	public function register_script( $script )
	{
		$this->set_script( preg_replace( '/<\s*\/?script\s*>/', '', $script ) );
	}

	public function register_js_file( $file )
	{
		$this->set_script( @file_get_contents( $file ) );
	}

	private function set_script( $script )
	{
		if ( $this->has_output_script ) {
			$this->script = $script;
			$this->output_js();
		} else {
			$this->script .= $script . "\n";
		}
	}

	private function output_js()
	{
		$this->script = $this->minify_js( $this->script );
		if ( "" === $this->script ) {
			return;
		}
		echo '<script>' . $this->script . '</script>';
		$this->script = '';
		$this->has_output_script = true;
	}

	public function register_css( $css, $scss = false )
	{
		$this->set_css( preg_replace( '/<\s*\/?style\s*>/', '', $css ), $scss );
	}

	public function register_css_file( $file, $scss = false )
	{
		$this->set_css( @file_get_contents( $file ), $scss );
	}

	private function set_css( $css, $scss = false )
	{
		if ( $scss ) {
			$css = $this->scss_to_css( $css );
		}
		if ( $this->end_footer ) {
			$this->css = $css;
			$this->output_css();
		} else {
			$this->css .= $css . "\n";
		}
	}

	private function output_css()
	{
		$this->css = $this->minify_css( $this->css );
		if ( "" === $this->css ) {
			return;
		}
		echo '<style>' . $this->css . '</style>';
		$this->css = '';
	}

	public function scss_to_css( $scss )
	{
		if ( !class_exists( '\scssc' ) ) {
			require_once UCB_RECOMMEND_LIB_LIBRARY_DIR . DIRECTORY_SEPARATOR . 'scssphp' . DIRECTORY_SEPARATOR . 'scss.inc.php';
		}
		$scssc = new \scssc();
		$scss = $scssc->compile( $scss );
		return $scss;
	}

	public function minify_js( $js )
	{
		$js = trim( preg_replace( '/<\s*\/?script\s*>/', '', $js ) );
		if ( "" === $js ) {
			return "";
		}
		if ( $this->apply_filters( "minify_js", UCB_RECOMMEND_MINIFY_JS ) ) {
			if ( !class_exists( '\JSMin' ) ) {
				require_once UCB_RECOMMEND_LIB_LIBRARY_DIR . DIRECTORY_SEPARATOR . 'jsmin-php' . DIRECTORY_SEPARATOR . 'jsmin.php';
			}
			return \JSMin::minify( $js );
		}
		return $js;
	}

	public function minify_css( $css )
	{
		$css = trim( preg_replace( '/<\s*\/?style\s*>/', '', $css ) );
		if ( "" === $css ) {
			return "";
		}
		if ( $this->apply_filters( 'minify_css', UCB_RECOMMEND_MINIFY_CSS ) ) {
			if ( !class_exists( '\CSSmin' ) ) {
				require_once UCB_RECOMMEND_LIB_LIBRARY_DIR . DIRECTORY_SEPARATOR . 'YUI-CSS-compressor' . DIRECTORY_SEPARATOR . 'cssmin.php';
			}
			$compressor = new \CSSmin();
			return $compressor->run( $css );
		}
		return $css;
	}
}

$GLOBALS['ucbr_minify'] = UCBRecommend_Minify::get_instance();
