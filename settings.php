<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

define( 'UCB_RECOMMEND_OUTPUT_LOG', true );
define( 'UCB_RECOMMEND_SHOW_ERROR', false );
define( 'UCB_RECOMMEND_NEED_NONCE_CHECK', false );

//db settings
define( 'UCB_RECOMMEND_TABLE_VERSION', '0.0.0.0.5' );

//cap
define( 'UCB_RECOMMEND_ADMIN_CAPABILITY', 'manage_options' );

//default settings
define( 'UCB_RECOMMEND_USER_EXPIRE', HOUR_IN_SECONDS );
define( 'UCB_RECOMMEND_UPDATE_COOKIE_EXPIRE', true );
define( 'UCB_RECOMMEND_SAMPLING_RATE', -1 );
define( 'UCB_RECOMMEND_CHECK_DATA', true );
define( 'UCB_RECOMMEND_NONCE_CHECK', UCB_RECOMMEND_NEED_NONCE_CHECK );
define( 'UCB_RECOMMEND_CALC_INTERVAL', MINUTE_IN_SECONDS * 10 );
define( 'UCB_RECOMMEND_CALC_TIMEOUT', MINUTE_IN_SECONDS * 10 );
define( 'UCB_RECOMMEND_CALC_LOG', true );
define( 'UCB_RECOMMEND_CLEAR_INTERVAL', HOUR_IN_SECONDS );
define( 'UCB_RECOMMEND_CLEAR_TIMEOUT', MINUTE_IN_SECONDS * 10 );
define( 'UCB_RECOMMEND_CLEAR_LOG', false );
define( 'UCB_RECOMMEND_DISPLAY_LOG_NUMBER', 100 );
define( 'UCB_RECOMMEND_MINIFY_JS', true );
define( 'UCB_RECOMMEND_MINIFY_CSS', true );
define( 'UCB_RECOMMEND_JACCARD_THRESHOLD', 0 );
define( 'UCB_RECOMMEND_JACCARD_MIN_NUMBER', 10 );
define( 'UCB_RECOMMEND_CALCULATE_NUMBER', 10000 );
define( 'UCB_RECOMMEND_SHOW_RESULT', true );

define( 'UCB_RECOMMEND_EXCLUDE_LOGGEDIN_USER', true );
define( 'UCB_RECOMMEND_DATA_EXPIRE', DAY_IN_SECONDS );
define( 'UCB_RECOMMEND_NO_THUMB_IMAGE', 'no_thumb.jpg' );
define( 'UCB_RECOMMEND_LOADING_IMAGE', 'loading.gif' );
define( 'UCB_RECOMMEND_GET_DATA_NUMBER', 10 );
define( 'UCB_RECOMMEND_NO_CONTEXT_MODE', false );
define( 'UCB_RECOMMEND_UCB_CONST', 1 );
define( 'UCB_RECOMMEND_PREVIEW_POST_NUMBER', 10 );
define( 'UCB_RECOMMEND_WIDGET_SHORTCODE', 'ucbr_widget' );
define( 'UCB_RECOMMEND_CONDITION_TEST_NUMBER', 30 );
define( 'UCB_RECOMMEND_VALID_DEVICES', 'a:0:{}' );
define( 'UCB_RECOMMEND_CUSTOM_VALID_DEVICE', '' );
define( 'UCB_RECOMMEND_FRONT_ADMIN_AJAX', false );
define( 'UCB_RECOMMEND_CHECK_REFERER', true );
define( 'UCB_RECOMMEND_BANDIT_RANDOM_STD_DEV', 0 );
define( 'UCB_RECOMMEND_TEST', true );
define( 'UCB_RECOMMEND_CONSIDER_PAGE_CACHE', true );

//from github
define( 'UCB_RECOMMEND_CHECK_UPDATE', false );

//redirect url
define( 'UCB_RECOMMEND_TEST_FILE',  'access' );

//ajax
define( 'UCB_RECOMMEND_AJAX_FILE', 'ajax' );

//default value
define( 'UCB_RECOMMEND_DEFAULT_SAMPLING_RATE', 1 );

