<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;
global $ucbr_minify;
$ucbr_minify->register_css( <<< EOS
.ucbr-submit{
	float:right;
	margin-top:10px!important;
}
EOS
);
?>
<h2><?php _e( "Setting", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h2>
<form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
	<table class="widefat striped">
		<tr>
			<th><?php _e( "Parameter", UCB_RECOMMEND_TEXT_DOMAIN ); ?></th>
			<th><?php _e( "Saved value", UCB_RECOMMEND_TEXT_DOMAIN ); ?></th>
			<th><?php _e( "Used value", UCB_RECOMMEND_TEXT_DOMAIN ); ?></th>
		</tr>
		<?php if ( count( $items ) <= 0 ): ?>
			<tr>
				<td colspan="3"><?php _e( "Item not found.", UCB_RECOMMEND_TEXT_DOMAIN ); ?></td>
			</tr>
		<?php else: ?>
			<?php foreach ( $items as $k => $v ): ?>
				<tr>
					<td><label for="<?php echo $k; ?>"><?php echo $v["label"]; ?></label></td>
					<td><input type="text" id="<?php echo $k; ?>" name="<?php echo $v['name']; ?>"
							   value="<?php echo esc_attr( $v["db"] ); ?>"
							   placeholder="<?php echo esc_attr( $v["placeholder"] ); ?>"></td>
					<td><?php echo esc_html( $v["used"] ); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</table>
	<input type="submit" value="<?php _e( "Setting", UCB_RECOMMEND_TEXT_DOMAIN ); ?>"
		   class="button-primary ucbr-submit">
	<input type="hidden" value="<?php echo $nonce; ?>" name="nonce">
</form>
