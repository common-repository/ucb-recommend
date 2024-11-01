<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;
?>
<script>
	var ucbr_obj = ucbr_obj || {};
	(function ($) {

		ucbr_obj.show_modal = function (loading, click, mes) {
			$("#ucbr-modal").fadeIn();
			if (loading) {
				$("#ucbr-modal .ucbr-loading").fadeIn();
				$("#ucbr-modal .ucbr-loading-message").fadeIn();
				if (mes) {
					$("#ucbr-modal .ucbr-loading-message").html(mes);
				}
			}
			$("#ucbr-modal-message-warp").fadeOut();
			if (click) {
				$("#ucbr-modal, #ucbr-modal-message-warp").unbind('click').click(function () {
					click();
					return false;
				});

			}
		};
		ucbr_obj.show_loading = function () {
			$("#ucbr-modal .ucbr-loading").fadeIn();
		};
		ucbr_obj.show_modal_message = function (mes) {
			if (mes) {
				ucbr_obj.set_modal_message(mes);
			}
			$("#ucbr-modal-message-warp").show();
			var check_resize = function () {
				if ($("#ucbr-modal-message-warp").is(":visible")) {
					ucbr_obj.set_modal_message_size();
					setTimeout(check_resize, 1000);
				}
			};
			setTimeout(function () {
				check_resize();
			}, 100);
		};
		ucbr_obj.hide_modal = function () {
			$("#ucbr-modal").fadeOut();
			$("#ucbr-modal .ucbr-loading").fadeOut();
			$("#ucbr-modal .ucbr-loading-message").fadeOut();
			$("#ucbr-modal-message-warp").fadeOut();
		};
		ucbr_obj.hide_loading = function () {
			$("#ucbr-modal .ucbr-loading").fadeOut();
			$("#ucbr-modal .ucbr-loading-message").fadeOut();
		};
		ucbr_obj.hide_modal_message = function () {
			$("#ucbr-modal-message-warp").fadeOut();
		};
		ucbr_obj.set_modal_message = function (mes) {
			$("#ucbr-modal-message").html(mes);
			ucbr_obj.set_modal_message_size();
		};
		ucbr_obj.set_modal_message_size = function () {
			var height = $("#ucbr-modal-message-warp").get(0).offsetHeight / 2;
			$("#ucbr-modal-message-warp").css('margin-top', -height + 'px');
		};
		$(function () {
			<?php if (is_admin()):?>
			$('<div id="ucbr-modal"><div class="ucbr-loading"></div>' + '<div class="ucbr-loading-message"></div>' + '</div>' + '<div id="ucbr-modal-message-warp">' + '<div id="ucbr-modal-message"></div>' + '</div>').prependTo("#wpwrap").hide();
			<?php else:?>
			$('<div id="ucbr-modal"><div class="ucbr-loading"></div>' + '<div class="ucbr-loading-message"></div>' + '</div>' + '<div id="ucbr-modal-message-warp">' + '<div id="ucbr-modal-message"></div>' + '</div>').prependTo("#container").hide();
			<?php endif;?>
			$('#ucbr-modal-message').click(function () {
				return false;
			});
		});
	})(jQuery);
</script>
