<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;
$loading_message = str_replace( '"', "'", __( 'loading', UCB_RECOMMEND_TEXT_DOMAIN ) ) . '...';
?>
<script>
	var ucbr_obj = ucbr_obj || {};
	ucbr_obj.test_executing = false;
	ucbr_obj.tests = null;
	ucbr_obj.now_loading = '<div class="ucbr-now-loading">Now Loading...<img src="<?php echo esc_attr( $loading_image );?>" class="ucbr-now-loading-img"></div>';
	ucbr_obj.test_number = 2;
	ucbr_obj.close_modal_func = null;

	(function ($) {

		function get_tests(func) {
			if (ucbr_obj.tests !== null) {
				if (func) func(ucbr_obj.tests);
				return;
			}
			ucbr_obj.get_tests({}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				ucbr_obj.tests = data;
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function do_test(elem, func) {
			if ($(elem).hasClass('ucbr-test-executing') || $(elem).hasClass('ucbr-test-executed')) {
				return;
			}
			$(elem).html(ucbr_obj.now_loading);
			$(elem).addClass('ucbr-test-executing');
			var method = $(elem).data('method');
			var params = $(elem).data('params');
			ucbr_obj[method](params, function (data) {
				set_test(elem, data);
				if (func) func(data);
			}, function (error) {
				set_error(error);
			}, function () {
				$(elem).addClass('ucbr-test-executed');
				$(elem).removeClass('ucbr-test-executing');
			});
		}

		function set_test(elem, data) {
			if (data.result) {
				$(elem).html('<span class="ucbr-ok">OK</span>');
				$(elem).data('result', 1);
			} else {
				$(elem).html('<span class="ucbr-ng">NG</span>');
				$(elem).data('result', 0);
			}
		}

		function reflect_results(r, func, finished) {
			ucbr_obj.reflect_results({r: r}, function (data) {
				if (func) func(data);
			}, function (error) {
				set_error(error);
			}, function () {
				if (finished) finished();
			});
		}

		function set_error(error) {
			console.log(error);
			ucbr_obj.hide_modal();
		}

		function parse_data(data, f, n) {
			var html = '';
			if (n === undefined) {
				n = 3;
			}
			for (var key in data) {
				if (f === undefined) {
					var func = key;
				} else {
					var func = f;
				}
				html += '<div data-id="ucbr-test-' + key + '">';
				if ('title' in data[key]) {
					html += '<h' + n + '>' + data[key]['title'] + '</h' + n + '>';
				}
				if ('groups' in data[key]) {
					html += parse_data(data[key]['groups'], func, n + 1);
				}
				if ('items' in data[key]) {
					html += '<div class="ucbr-test-group" data-method="' + func + '" data-group="' + key + '">';
					for (var i in data[key]['items']) {
						html += '<div class="ucbr-test-item" data-method="' + func + '" data-group="' + key + '"';
						html += ' data-params=\'{';
						var first = true;
						for (var j in data[key]['items'][i]) {
							if (first)
								first = false;
							else
								html += ',';
							html += '"' + j + '":' + data[key]['items'][i][j];
						}
						html += '}\'>';
						html += '</div>';
					}
					html += '</div>';
				}
				html += '</div>';
			}
			return html;
		}

		function end_test(func) {
			ucbr_obj.end_test({}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func)func(data);
			}, function (error) {
				set_error(error);
			});
		}

		$(function () {
			$('#ucbr-test-button').click(function () {
				if (ucbr_obj.test_executing) {
					return;
				}
				ucbr_obj.test_executing = true;
				ucbr_obj.close_modal_func = null;
				ucbr_obj.show_modal(true, function () {
					ucbr_obj.test_executing = false;
					if (ucbr_obj.close_modal_func) {
						ucbr_obj.close_modal_func();
					}
					ucbr_obj.hide_modal();
				}, "<?php echo esc_js( $loading_message );?>");

				get_tests(function (data) {
					if (!ucbr_obj.test_executing) {
						return;
					}
					var html = '<div id="ucbr-test">';
					html += parse_data(data.result);
					html += '</div>';
					ucbr_obj.hide_loading();
					ucbr_obj.show_modal_message(html);

					ucbr_obj.timer = setInterval(function () {
						if (!ucbr_obj.test_executing) {
							clearInterval(ucbr_obj.timer);
							return;
						}
						var rest = $('.ucbr-test-item').not('.ucbr-test-executed, .ucbr-test-executing').length;
						if (rest <= 0) {
							clearInterval(ucbr_obj.timer);
							ucbr_obj.timer = setInterval(function () {
								if (!ucbr_obj.test_executing) {
									clearInterval(ucbr_obj.timer);
									return;
								}
								var rest = $('.ucbr-test-executing').length;
								if (rest <= 0) {
									clearInterval(ucbr_obj.timer);

									var r = {};
									$('.ucbr-test-group').each(function () {
										var method = $(this).data('method');
										var group = $(this).data('group');
										if (r[method] === undefined) {
											r[method] = {};
										}
										r[method][group] = {};
										$(this).find('.ucbr-test-item').each(function (index) {
											r[method][group][index] = $(this).data('result');
										});
									});
									$('.ucbr-test-group').html(ucbr_obj.now_loading);

									reflect_results(r, function (data) {
										if (!ucbr_obj.test_executing) {
											return;
										}
										var error = false;
										for (var key in data.result.results) {
											var html = '';
											if (data.result.results[key].result) {
												html += '<span class="ucbr-ok">' + data.result.results[key].message + '</span>';
											} else {
												html += '<span class="ucbr-ng">' + data.result.results[key].message + '</span>';
												error = true;
											}
											$('.ucbr-test-group[data-group="' + key + '"]').html(html);
										}

										var elem = $('#ucbr-test-button').closest('.ucbr-admin-message');
										$(elem).fadeOut();
										var modal = $('#ucbr-test');
										if (data.result.fatal) {
											$(modal).append('<input type="button" value="プラグインページへ" id="ucbr-test-plugin-page">');
											$('#ucbr-test-plugin-page').click(function () {
												location.href = data.result.urls.plugin;
												return false;
											});
										}
										$(modal).append('<input type="button" value="閉じる" id="ucbr-test-close-button">');
										ucbr_obj.close_modal_func = function () {
											if (location.href == data.result.urls.setting) {
												location.reload();
											} else {
												ucbr_obj.hide_modal();
											}
										};
										$('#ucbr-test-close-button').click(function () {
											ucbr_obj.close_modal_func();
											return false;
										});
									}, function () {
										ucbr_obj.test_executing = false;
									});
								}
							}, 500);
							return;
						}
						var executing = $('.ucbr-test-executing').length;
						for (var i = ucbr_obj.test_number - executing; --i >= 0 && --rest >= 0;) {
							do_test($('.ucbr-test-item').not('.ucbr-test-executed, .ucbr-test-executing').eq(0), function (data) {
							});
						}
					}, 500);
				});
			})
		});
	})(jQuery);
</script>
