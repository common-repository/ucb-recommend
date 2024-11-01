<?php
namespace UCBRecommend;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

define( 'UCB_RECOMMEND_ROOT_DIR', UCB_RECOMMEND_PLUGIN_DIR );
define( 'UCB_RECOMMEND_LIB_ROOT_DIR', UCB_RECOMMEND_ROOT_DIR . DIRECTORY_SEPARATOR . "lib" );
define( 'UCB_RECOMMEND_COMMON_DIR', UCB_RECOMMEND_ROOT_DIR . DIRECTORY_SEPARATOR . "common" );
define( 'UCB_RECOMMEND_LIB_COMMON_DIR', UCB_RECOMMEND_LIB_ROOT_DIR . DIRECTORY_SEPARATOR . "common" );

//settings.php
@require_once( UCB_RECOMMEND_ROOT_DIR . DIRECTORY_SEPARATOR . "settings.php" );

function ucbr_scandir( $dir )
{
	if ( is_dir( $dir ) ) {
		foreach ( scandir( $dir ) as $file ) {
			if ( preg_match( "/^[^\\.].*\\.php$/", $file ) ) {
				require_once $dir . DIRECTORY_SEPARATOR . $file;
			}
		}
	}
}

//common
ucbr_scandir( UCB_RECOMMEND_LIB_COMMON_DIR );

//models
ucbr_scandir( UCB_RECOMMEND_LIB_MODELS_DIR );

@require_once( UCB_RECOMMEND_ROOT_DIR . DIRECTORY_SEPARATOR . "db-config.php" );

foreach ( array( UCB_RECOMMEND_LIB_SERVICES_DIR, UCB_RECOMMEND_COMMON_DIR, UCB_RECOMMEND_MODELS_DIR, UCB_RECOMMEND_SERVICES_DIR ) as $dir ) {
	ucbr_scandir( $dir );
}

