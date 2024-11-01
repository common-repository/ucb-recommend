<?php

if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'ucb-recommend.php' );

global $ucbr_db, $ucbr_option, $ucbr_post, $ucbr_user;
if ( !is_multisite() ) {
	$ucbr_db->uninstall();
	$ucbr_option->uninstall();
	$ucbr_post->uninstall();
	$ucbr_user->uninstall();

	wp_clear_scheduled_hook( 'ucbr_clear_event' );
	wp_clear_scheduled_hook( 'ucbr_clear_hook' );
} else {
	global $wpdb;
	$current_blog_id = get_current_blog_id();
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );

		$ucbr_db->uninstall();
		$ucbr_option->uninstall();
		$ucbr_post->uninstall();
		$ucbr_user->uninstall();

		wp_clear_scheduled_hook( 'ucbr_clear_event' );
		wp_clear_scheduled_hook( 'ucbr_clear_hook' );
	}
	switch_to_blog( $current_blog_id );
}

