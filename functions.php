<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

add_action( 'template_redirect', function () {
	if ( false === has_filter( 'widget_text', 'do_shortcode' ) ) {
		add_filter( 'widget_text', 'do_shortcode' );
	}
} );
