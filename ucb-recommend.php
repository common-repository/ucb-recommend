<?php
/*
  Plugin Name: UCB Recommend
  Plugin URI: https://wordpress.org/plugins/ucb-recommend/
  Description: Recommendation and AB test plugin using ucb algorithm
  Author: 123teru321
  Version: 1.1.3
  Author URI: http://technote.space/
  Text Domain: UCBRecommend
  Domain Path: /languages
*/

if ( !defined( 'ABSPATH' ) )
	exit;

if ( defined( 'UCB_RECOMMEND_PLUGIN' ) )
	return;

if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING )
	return;

//plugin
define( 'UCB_RECOMMEND_PLUGIN', 'UCB_RECOMMEND_PLUGIN' );

//plugin name
define( 'UCB_RECOMMEND_PLUGIN_NAME', 'UCB Recommend' );

//plugin version
define( 'UCB_RECOMMEND_PLUGIN_VERSION', '1.1.3' );

//required php version
define( 'UCB_RECOMMEND_REQUIRED_PHP_VERSION', '5.4' );

//plugin file name
define( 'UCB_RECOMMEND_PLUGIN_FILE_NAME', __FILE__ );

//plugin directory
define( 'UCB_RECOMMEND_PLUGIN_DIR', dirname( UCB_RECOMMEND_PLUGIN_FILE_NAME ) );

//plugin directory name
define( 'UCB_RECOMMEND_PLUGIN_DIR_NAME', basename( UCB_RECOMMEND_PLUGIN_DIR ) );

//plugin base name
define( 'UCB_RECOMMEND_PLUGIN_BASE_NAME', plugin_basename( UCB_RECOMMEND_PLUGIN_FILE_NAME ) );

//text domain
define( 'UCB_RECOMMEND_TEXT_DOMAIN', 'UCBRecommend' );
load_plugin_textdomain( UCB_RECOMMEND_TEXT_DOMAIN, false, UCB_RECOMMEND_PLUGIN_DIR_NAME . DIRECTORY_SEPARATOR . 'languages' );

if ( version_compare( phpversion(), UCB_RECOMMEND_REQUIRED_PHP_VERSION, '<' ) ) {
	// php version isn't high enough
	require_once 'unsupported.php';
	return;
}

//load
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "load.php";

//functions.php
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "functions.php";


