<?php
if (!defined('UCB_RECOMMEND_PLUGIN')) exit;
?>
<script>
	var ucbr_obj = ucbr_obj || {};

	(function ($) {
		ucbr_obj.disable_controls = function () {
			ucbr_obj.control_enabled = false;
			$("#ucbr-main-contents input, #ucbr-main-contents textarea, #ucbr-main-contents select, .ucbr-enable-control").not(".disabled-control").attr("disabled", "disabled");
		};
		ucbr_obj.enable_controls = function () {
			ucbr_obj.control_enabled = true;
			$("#ucbr-main-contents input, #ucbr-main-contents textarea, #ucbr-main-contents select, .ucbr-enable-control").not(".disabled-control").removeAttr("disabled");
		};
	})(jQuery);
</script>
