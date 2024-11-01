<?php

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) ) {
	die();
}

function ucbr_old_php_message()
{
	$ret = sprintf( __( 'Your PHP version is %s.', UCB_RECOMMEND_TEXT_DOMAIN ), phpversion() ) . '<br>';
	$ret .= __( 'Please update your PHP.', UCB_RECOMMEND_TEXT_DOMAIN ) . '<br>';
	$ret .= sprintf( __( '<strong>%s</strong> requires PHP version %s or above.', UCB_RECOMMEND_TEXT_DOMAIN ), UCB_RECOMMEND_PLUGIN_NAME, UCB_RECOMMEND_REQUIRED_PHP_VERSION );
	return $ret;
}

function ucbr_old_php_admin_notices()
{
	?>
	<div class="notice error notice-error">
		<p><?php echo ucbr_old_php_message(); ?></p>
	</div>
	<?php
}

add_action( 'admin_notices', 'ucbr_old_php_admin_notices' );
