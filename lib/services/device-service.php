<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_DeviceInfo extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	private $list = array(
		'iPhone' => array( 'iPhone', 'is_iphone' ),
		'Android' => array( 'Android', 'is_android' ),
		'WindowsPhone' => array( 'Windows Phone', 'is_windows_phone' ),
		'BlackBerry' => array( 'BlackBerry', 'is_blackberry' ),
		'iPad' => array( 'iPad', 'is_ipad' ),
		'AndroidTablet' => array( 'Android Tablet', 'is_android_tab' ),
		'SmartPhone' => array( 'Smartphone', 'is_smartphone' ),
		'Tablet' => array( 'Tablet', 'is_tablet' ),
		'PC' => array( 'PC', 'is_pc' )
	);

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_DeviceInfo();
		}
		return self::$_instance;
	}

	public function get_android_version()
	{
		preg_match( "/(Android\\s([0-9\\.]*))/", $_SERVER['HTTP_USER_AGENT'], $result );
		if ( count( $result ) ) {
			$version = $result[2];
			list( $major, $minor ) = explode( ".", $version );

			return (float)( $major . '.' . $minor );
		}
		return 0;
	}

	public function get_IE_version()
	{
		$ua = $_SERVER['HTTP_USER_AGENT'];
		$results['browser'] = '';
		$results['version'] = '';
		if ( preg_match( '/Trident\/(\d{1,}(.\d{1,}){1,}?)/i', $ua, $mtcs ) ) {
			$results['browser'] = 'msie';
			if ( (float)$mtcs[1] >= 7 ) {
				if ( preg_match( '/rv:(\d{1,}(.\d{1,}){1,}?)/i', $ua, $mtcs ) ) {
					$results['version'] = (float)$mtcs[1];
				} else {
					$results['version'] = 11.0;
				}
			} elseif ( (float)$mtcs[1] >= 6 ) {
				$results['version'] = 10.0;
			} elseif ( (float)$mtcs[1] >= 5 ) {
				$results['version'] = 9.0;
			} elseif ( (float)$mtcs[1] >= 4 ) {
				$results['version'] = 8.0;
			}
		}
		if ( empty( $results['browser'] ) ) {
			if ( preg_match( '/MSIE\s(\d{1,}(.\d{1,}){1,}?);/i', $ua, $mtcs ) ) {
				$results['browser'] = 'msie';
				$results['version'] = (float)$mtcs[1];
			}
		}
		return $results;
	}

	public function is_iphone()
	{
		$ua = $_SERVER["HTTP_USER_AGENT"];

		if ( strpos( $ua, 'iPhone' ) !== false ) {
			return true;
		}
		return false;
	}

	public function is_android()
	{
		$ua = $_SERVER["HTTP_USER_AGENT"];

		if ( strpos( $ua, 'Android' ) !== false && strpos( $ua, 'Mobile' ) !== false ) {
			return true;
		}
		return false;
	}

	public function is_windows_phone()
	{
		$ua = $_SERVER["HTTP_USER_AGENT"];

		if ( strpos( $ua, 'Windows Phone' ) !== false ) {
			return true;
		}
		return false;
	}

	public function is_blackberry()
	{
		$ua = $_SERVER["HTTP_USER_AGENT"];

		if ( strpos( $ua, 'BlackBerry' ) !== false ) {
			return true;
		}
		return false;
	}

	public function is_ipad()
	{
		$ua = $_SERVER["HTTP_USER_AGENT"];

		if ( strpos( $ua, 'iPad' ) !== false ) {
			return true;
		}
		return false;
	}

	public function is_android_tab()
	{
		$ua = $_SERVER["HTTP_USER_AGENT"];

		if ( strpos( $ua, 'Android' ) !== false && strpos( $ua, 'Mobile' ) === false ) {
			return true;
		}
		return false;
	}

	public function is_smartphone()
	{
		if ( $this->is_iphone() ) {
			return true;
		}
		if ( $this->is_android() ) {
			return true;
		}
		if ( $this->is_windows_phone() ) {
			return true;
		}
		if ( $this->is_blackberry() ) {
			return true;
		}
		return false;
	}

	public function is_tablet()
	{
		if ( $this->is_ipad() ) {
			return true;
		}
		if ( $this->is_android_tab() ) {
			return true;
		}
		return false;
	}

	public function is_pc()
	{
		if ( $this->is_smartphone() ) {
			return false;
		}
		if ( $this->is_tablet() ) {
			return false;
		}
		return true;
	}

	public function is_bot()
	{
		$check = $this->apply_filters( "pre_check_bot", null );
		if ( is_bool( $check ) ) {
			return $check;
		}

		$bot_list = $this->apply_filters( "bot_list", array(
			"facebookexternalhit",
			"Googlebot",
			"Baiduspider",
			"bingbot",
			"Yeti",
			"NaverBot",
			"Yahoo! Slurp",
			"Y!J-BRI",
			"Y!J-BRJ\\/YATS crawler",
			"Tumblr",
			//		"livedoor",
			//		"Hatena",
			"Twitterbot",
			"Page Speed",
			"Google Web Preview",
		) );

		$ua = $_SERVER["HTTP_USER_AGENT"];
		foreach ( $bot_list as $value ) {
			if ( preg_match( '/' . $value . '/i', $ua ) ) {
				return true;
			}
		}
		return false;
	}

	public function check( $data )
	{
		$data = explode( "\n", $data );
		$data = array_map( 'trim', $data );
		$data = array_filter( $data );
		$data = array_unique( $data );
		$ua = $_SERVER["HTTP_USER_AGENT"];
		foreach ( $data as $value ) {
			if ( preg_match( $value, $ua ) ) {
				return true;
			}
		}
		return false;
	}

	public function get_device_list()
	{
		static $list = null;
		if ( !is_null( $list ) ) {
			return $list;
		}
		$list = $this->apply_filters( 'device_list', $this->list );
		foreach ( $list as $k => $v ) {
			$v[0] = __( $v[0], UCB_RECOMMEND_TEXT_DOMAIN );
			$list[$k] = $v;
		}
		return $list;
	}

	public function get_device( $key )
	{
		$list = $this->get_device_list();
		if ( array_key_exists( $key, $list ) ) {
			return $list[$key];
		}
		return null;
	}

	public function get_device_name( $key )
	{
		$device = $this->get_device( $key );
		if ( $device ) {
			return $device[0];
		}
		return '';
	}

	public function check_device( $key )
	{
		$device = $this->get_device( $key );
		if ( $device ) {
			$check = $device[1];
			if ( is_callable( array( $this, $check ) ) ) {
				return $this->$check();
			}
		}
		return false;
	}
}

$GLOBALS['ucbr_deviceinfo'] = UCBRecommend_DeviceInfo::get_instance();
