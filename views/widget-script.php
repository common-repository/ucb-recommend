<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;
?>
<script>
	var ucbr_obj = ucbr_obj || {};
	(function ($) {
		$(function () {
			ucbr_obj.load_widget = function (elem) {
				var id = $(elem).data('id');
				ucbr_obj.widget({id: id, p: <?php echo $post_id;?>}, function (data) {
					if (!data.result) {
						//	console.log(data.message);
						$(elem).remove();
						return;
					}
					<?php if ($fadeIn > 0):?>
					$(elem).hide().html(data.message).fadeIn(<?php echo $fadeIn;?>);
					<?php else:?>
					$(elem).html(data.message);
					<?php endif;?>
				}, function (error) {
					$(elem).remove();
					console.log(error);
				});
			};
			<?php if( $lazy ):?>
			$('.ucbr-widget-load').on('inview', function (event, is_in_view) {
				if (!is_in_view) {
					return;
				}
				$(this).off('inview');
				ucbr_obj.load_widget(this);
			});
			<?php else:?>
			$('.ucbr-widget-load').each(function () {
				ucbr_obj.load_widget(this);
			});
			<?php endif;?>
		});
	})(jQuery);
</script>
