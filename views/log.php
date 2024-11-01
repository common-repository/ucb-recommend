<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;
?>
<h2><?php _e( "Log", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h2>
<table class="widefat striped">
	<tr>
		<th><?php _e( "Date", UCB_RECOMMEND_TEXT_DOMAIN ); ?></th>
		<th><?php _e( "Message", UCB_RECOMMEND_TEXT_DOMAIN ); ?></th>
	</tr>
	<?php if ( count( $date ) !== count( $message ) ): ?>
		<tr>
			<td colspan="2"><?php _e( "The log file was broken and could not be opened.", UCB_RECOMMEND_TEXT_DOMAIN ); ?></td>
		</tr>
	<?php elseif ( count( $date ) <= 0 ): ?>
		<tr>
			<td colspan="2"><?php _e( "Item not found.", UCB_RECOMMEND_TEXT_DOMAIN ); ?></td>
		</tr>
	<?php else: ?>
		<?php for ( $i = count( $date ); --$i >= 0 && --$number >= 0; ) : ?>
			<tr>
				<td><?php echo $date[$i]; ?></td>
				<td><?php echo nl2br( $message[$i] ); ?></td>
			</tr>
		<?php endfor; ?>
	<?php endif; ?>
</table>


