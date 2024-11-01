<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;
?>
<script>
	var ucbr_obj = ucbr_obj || {};
	(function ($) {
		ucbr_obj.tab_id = -1;
		ucbr_obj.types = null;
		ucbr_obj.verbs = {};
		ucbr_obj.tables = null;
		ucbr_obj.columns = {};
		ucbr_obj.condition_set = null;
		ucbr_obj.join_types = null;
		ucbr_obj.devices = null;
		ucbr_obj.widget_id = 0;
		ucbr_obj.condition_group_id = 0;
		ucbr_obj.condition_id = 0;
		ucbr_obj.object_id = 0;
		ucbr_obj.now_loading = '<div class="ucbr-now-loading">Now Loading...<img src="<?php echo esc_attr( $loading_image );?>" class="ucbr-now-loading-img"></div>';
		ucbr_obj.message_timer = null;
		ucbr_obj.prev_type = null;
		ucbr_obj.prev_verb = {};
		function initialize() {
			ucbr_obj.disable_controls();
			$('#ucbr-main').html(ucbr_obj.now_loading);
			get_widgets(false, function () {
				ucbr_obj.enable_controls();
			});

			$('#ucbr-new-widget-button').click(function () {
				var name = $('#ucbr-new-widget-name').val();
				if (name.length <= 0) {
					return false;
				}
				$('#ucbr-new-widget-name').val('');
				ucbr_obj.disable_controls();

				$('#ucbr-main').html(ucbr_obj.now_loading);
				$('#ucbr-select-widget').html(ucbr_obj.now_loading);
				save_widget('', name, function (data) {
					ucbr_obj.enable_controls();
					get_widgets(data.result.uuid);
				});
				return false;
			});

			$('#ucbr-close-menu-button').click(function () {
				if ($(this).hasClass('ucbr-closed')) {
					$(this).removeClass('ucbr-closed');
					$(this).val('<?php _e( "Close", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
					$('.ucbr-widget-menu-item').fadeIn();
				} else {
					$(this).addClass('ucbr-closed');
					$(this).val('<?php _e( "Open", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
					$('.ucbr-widget-menu-item').hide(400);
				}
				return false;
			});
		}

		function set_explanation() {
			$('#ucbr-main').html('<?php _e( "Please create new widget.", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
		}

		function get_widget_id() {
			return ucbr_obj.widget_id;
		}

		function get_widget_uuid() {
			return $('#ucbr-select-widget-items option[value="' + get_widget_id() + '"]').data('uuid');
		}

		function get_widgets(selected, func) {
			$('#ucbr-select-widget').html(ucbr_obj.now_loading);
			ucbr_obj.get_widgets({}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (data.result.length <= 0) {
					$('#ucbr-select-widget').text('<?php _e( "Item not found.", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
					set_explanation();
				} else {
					var html = '<select id="ucbr-select-widget-items">';
					for (var key in data.result) {
						html += '<option value="' + data.result[key].id + '" data-uuid="' + data.result[key].uuid + '">';
						html += data.result[key].name;
						html += '</option>';
					}
					html += '</select>';
					html += '<input type="button" value="<?php _e( "Delete", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-delete-widget-button">';
					$('#ucbr-select-widget').html(html);
					$('#ucbr-select-widget-items').change(function () {
						var id = $(this).val();
						set_widget(id);
					});
					$('#ucbr-delete-widget-button').click(function () {
						if (!window.confirm('<?php _e( "Are you sure you want?", UCB_RECOMMEND_TEXT_DOMAIN ); ?>')) {
							return false;
						}
						ucbr_obj.disable_controls();
						var id = $('#ucbr-select-widget-items option:selected').data('uuid');
						$('#ucbr-select-widget').html(ucbr_obj.now_loading);
						$('#ucbr-main').html(ucbr_obj.now_loading);
						delete_widget(id, function () {
							get_widgets(false, function () {
								ucbr_obj.enable_controls();
							});
						});
						return false;
					});
					if (selected) {
						var s = $('#ucbr-select-widget-items [data-uuid="' + selected + '"]');
					} else {
						var s = $('#ucbr-select-widget-items option:first');
					}
					if (s.length > 0) {
						$('#ucbr-select-widget-items').val($(s).val()).trigger('change');
					} else {
						set_explanation();
					}
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function save_widget(id, name, func) {
			ucbr_obj.save_widget({id: id, n: name}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function delete_widget(id, func) {
			ucbr_obj.delete_widget({id: id}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function set_widget(id, func) {
			clear_message();
			ucbr_obj.widget_id = id;

			var html = '';
			html += '<h2 class="nav-tab-wrapper">';
			html += '<div class="nav-tab" data-id="0"><?php _e( "Dashboard", UCB_RECOMMEND_TEXT_DOMAIN ); ?></div>';
			html += '<div class="nav-tab" data-id="1"><?php _e( "Design", UCB_RECOMMEND_TEXT_DOMAIN ); ?></div>';
			html += '<div class="nav-tab" data-id="2"><?php _e( "Preview", UCB_RECOMMEND_TEXT_DOMAIN ); ?></div>';
			html += '<div class="nav-tab" data-id="3"><?php _e( "Display conditions", UCB_RECOMMEND_TEXT_DOMAIN ); ?></div>';
			html += '<div class="nav-tab" data-id="4"><?php _e( "Search conditions", UCB_RECOMMEND_TEXT_DOMAIN ); ?></div>';
			html += '<div class="nav-tab" data-id="5"><?php _e( "Join conditions", UCB_RECOMMEND_TEXT_DOMAIN ); ?></div>';
			html += '<div class="nav-tab" data-id="6"><?php _e( "Histories", UCB_RECOMMEND_TEXT_DOMAIN ); ?></div>';
			html += '<div class="nav-tab" data-id="7"><?php _e( "Help", UCB_RECOMMEND_TEXT_DOMAIN ); ?></div>';
			html += '</h2>';
			html += '<div id="ucbr-board"></div>';
			$('#ucbr-main').html(html);
			$('.nav-tab').click(function () {
				if (!ucbr_obj.control_enabled) {
					return false;
				}
				$('#ucbr-main .nav-tab').removeClass('nav-tab-active');
				$(this).addClass('nav-tab-active');
				ucbr_obj.disable_controls();
				$('#ucbr-board').html(ucbr_obj.now_loading);
				set_tab($(this).data('id'));
				return false;
			});
			ucbr_obj.control_enabled = true;
			$('.nav-tab:first').trigger('click');

			if (func) func();
		}

		function set_tab(id) {
			clear_message();
			$('#ucbr-widget-menu-general-item').html('');
			ucbr_obj.tab_id = id;
			switch (id) {
				case 1:
					set_design(id);
					break;
				case 2:
					set_preview(id);
					break;
				case 3:
					set_display_conditions(id);
					break;
				case 4:
					set_conditions(id);
					break;
				case 5:
					set_join_tables(id);
					break;
				case 6:
					set_histories(id);
					break;
				case 7:
					set_help(id);
					break;
				default:
					set_dashboard(id);
					break;
			}
		}

		function set_dashboard(tab_id) {
			if (tab_id !== ucbr_obj.tab_id) {
				return;
			}
			//統計情報とか後で
			ucbr_obj.enable_controls();
			var html = '';
			html += '<ul>';
			html += '<li>';
			html += '<h3><?php _e( "Shortcode", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
			html += "<input type='text' value='[<?php echo esc_attr( $shortcode );?> id=\"" + get_widget_id() + "\"]' readonly='readonly'>";
			html += '</li>';
			html += '<li class="ucbr-widget-setting" data-name="data_number" data-type="number" data-min="1">';
			html += '<h3><?php _e( "Display number", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
			html += '<span class="ucbr-widget-setting-wrap"></span>';
			html += '<input type="button" class="ucbr-save-widget-setting-button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>">';
			html += '<input type="button" class="ucbr-init-widget-setting-button" value="<?php _e( "Init", UCB_RECOMMEND_TEXT_DOMAIN ); ?>">';
			html += '</li>';
			html += '<li class="ucbr-widget-setting" data-name="no_context_mode" data-type="bool">';
			html += '<h3><?php _e( "No context mode", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
			html += '<span class="ucbr-widget-setting-wrap"></span>';
			html += '<input type="button" class="ucbr-save-widget-setting-button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>">';
			html += '<input type="button" class="ucbr-init-widget-setting-button" value="<?php _e( "Init", UCB_RECOMMEND_TEXT_DOMAIN ); ?>">';
			html += '</li>';
			html += '</ul>';
			$('#ucbr-board').html(html);

			$('.ucbr-widget-setting').each(function () {
				var $this = this;
				set_widget_setting(this);
				$(this).find('.ucbr-save-widget-setting-button').click(function () {
					var name = $($this).data('name');
					var value = $($this).find('.ucbr-widget-setting-value').val();
					$($this).find('.ucbr-widget-setting-wrap').html(ucbr_obj.now_loading);
					$($this).find('input[type="button"]').attr("disabled", "disabled");
					save_widget_setting(get_widget_id(), name, value, function () {
						set_updated('<?php _e( "Saved.", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
						set_widget_setting($this);
					});
					return false;
				});
				$(this).find('.ucbr-init-widget-setting-button').click(function () {
					var name = $($this).data('name');
					$($this).find('.ucbr-widget-setting-wrap').html(ucbr_obj.now_loading);
					$($this).find('input[type="button"]').attr("disabled", "disabled");
					init_widget_setting(get_widget_id(), name, function () {
						set_updated('<?php _e( "Initialized.", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
						set_widget_setting($this);
					});
					return false;
				});
			});
		}

		function set_widget_setting(elem) {
			var name = $(elem).data('name');
			var type = $(elem).data('type');
			$(elem).find('.ucbr-widget-setting-wrap').html(ucbr_obj.now_loading);
			$(elem).find('input[type="button"]').attr("disabled", "disabled");
			get_widget_setting(get_widget_id(), name, function (data) {
				var html = '';
				switch (type) {
					case 'text':
						html = '<input type="text" class="ucbr-widget-setting-value" value="' + data.result.value + '">';
						break;
					case 'number':
						var min = $(elem).data('min');
						var max = $(elem).data('max');
						html = '<input type="number" class="ucbr-widget-setting-value" value="' + data.result.value + '"';
						if ('' !== min) {
							html += ' min="' + min + '"';
						}
						if ('' !== max) {
							html += ' max="' + max + '"';
						}
						html += '>';
						break;
					case 'bool':
						var on = $(elem).data('on');
						var off = $(elem).data('off');
						if (undefined === on) {
							on = 'ON';
						}
						if (undefined === off) {
							off = 'OFF';
						}
						html += '<select class="ucbr-widget-setting-value">';
						html += '<option value="1"';
						if (data.result.value) {
							html += ' selected="selected"';
						}
						html += '>';
						html += on;
						html += '</option>';
						html += '<option value="0"';
						if (!data.result.value) {
							html += ' selected="selected"';
						}
						html += '>';
						html += off;
						html += '</option>';
						html += '</select>';
						break;
				}
				$(elem).find('.ucbr-widget-setting-wrap').html(html);
				$(elem).find('input[type="button"]').removeAttr("disabled");
			});
		}

		function set_design(tab_id) {
			if (tab_id !== ucbr_obj.tab_id) {
				return;
			}
			ucbr_obj.get_design_templates({id: get_widget_id()}, function (data) {
				if (tab_id !== ucbr_obj.tab_id) {
					return;
				}
				if (!data.result) {
					set_error(data.message);
					return;
				}
				var html = '';
				html += '<ul>';
				html += '<li data-name="load_widget">';
				html += '<h3><?php _e( "Loading template", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
				html += '<textarea wrap="off" class="ucbr-template-value">' + data.result.load_widget + '</textarea><br>';
				html += '<input type="button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-save-template-button">';
				html += '<input type="button" value="<?php _e( "Init", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-init-template-button">';
				html += '</li>';
				html += '<li data-name="list">';
				html += '<h3><?php _e( "List template", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
				html += '<textarea wrap="off" class="ucbr-template-value">' + data.result.list + '</textarea><br>';
				html += '<input type="button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-save-template-button">';
				html += '<input type="button" value="<?php _e( "Init", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-init-template-button">';
				html += '</li>';
				html += '<li data-name="item">';
				html += '<h3><?php _e( "Item template", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
				html += '<textarea wrap="off" class="ucbr-template-value">' + data.result.item + '</textarea><br>';
				html += '<input type="button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-save-template-button">';
				html += '<input type="button" value="<?php _e( "Init", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-init-template-button">';
				html += '</li>';
				html += '<li data-name="not_found">';
				html += '<h3><?php _e( "Not Found template", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
				html += '<textarea wrap="off" class="ucbr-template-value">' + data.result.not_found + '</textarea><br>';
				html += '<input type="button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-save-template-button">';
				html += '<input type="button" value="<?php _e( "Init", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-init-template-button">';
				html += '</li>';
				html += '<li data-name="style">';
				html += '<h3><?php _e( "Style template", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
				html += '<textarea wrap="off" class="ucbr-template-value">' + data.result.style + '</textarea><br>';
				html += '<input type="button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-save-template-button">';
				html += '<input type="button" value="<?php _e( "Init", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-init-template-button">';
				html += '</li>';
				html += '<li data-name="no_thumb">';
				html += '<h3><?php _e( "No thumbnail image", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
				html += '<img src="' + data.result.no_thumb + '" id="ucbr-no-thumb-image"><br>';
				html += '<input type="text" value="' + data.result.no_thumb + '" class="ucbr-template-value" id="ucbr-no-thumb-image-url"><br>';
				html += '<input type="button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-save-template-button">';
				html += '<input type="button" value="<?php _e( "Init", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-init-template-button">';
				html += '</li>';
				html += '<li data-name="loading">';
				html += '<h3><?php _e( "Loading image", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
				html += '<img src="' + data.result.loading + '" id="ucbr-loading-image"><br>';
				html += '<input type="text" value="' + data.result.loading + '" class="ucbr-template-value" id="ucbr-loading-image-url"><br>';
				html += '<input type="button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-save-template-button">';
				html += '<input type="button" value="<?php _e( "Init", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-init-template-button">';
				html += '</li>';
				html += '</ul>';
				$('#ucbr-board').html(html);
				$('#ucbr-board textarea').autosize();
				$('.ucbr-save-template-button').click(function () {
					ucbr_obj.disable_controls();
					var li = $(this).closest('li');
					var name = $(li).data('name');
					var value = $(li).find('.ucbr-template-value').val();
					save_design_template(get_widget_id(), name, value, function () {
						//set_design(tab_id);
						update_design_template(tab_id, name, true);
						set_updated('<?php _e( "Saved.", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
					});
					return false;
				});
				$('.ucbr-init-template-button').click(function () {
					ucbr_obj.disable_controls();
					var li = $(this).closest('li');
					var name = $(li).data('name');
					init_design_template(get_widget_id(), name, function () {
						update_design_template(tab_id, name, true);
						set_updated('<?php _e( "Initialized.", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
					});
					return false;
				});
				$('#ucbr-no-thumb-image-url').blur(function () {
					$('#ucbr-no-thumb-image').attr('src', $(this).val());
				});
				$('#ucbr-loading-image-url').blur(function () {
					$('#ucbr-loading-image').attr('src', $(this).val());
				});

				var html = '';
				html += '<input type="button" id="ucbr-check-design-button" value="<?php _e( "Check Design", UCB_RECOMMEND_TEXT_DOMAIN ); ?>">';
				html += '<input type="button" id="ucbr-save-design-button" value="<?php _e( "Save All", UCB_RECOMMEND_TEXT_DOMAIN ); ?>">';
				$('#ucbr-widget-menu-general-item').html(html);
				$('#ucbr-check-design-button').click(function () {
					ucbr_obj.disable_controls();
					var load_widget = $('li[data-name="load_widget"]').find('.ucbr-template-value').val();
					var loading = $('li[data-name="loading"]').find('.ucbr-template-value').val();
					var list = $('li[data-name="list"]').find('.ucbr-template-value').val();
					var item = $('li[data-name="item"]').find('.ucbr-template-value').val();
					var not_found = $('li[data-name="not_found"]').find('.ucbr-template-value').val();
					var style = $('li[data-name="style"]').find('.ucbr-template-value').val();
					var no_thumb = $('li[data-name="no_thumb"]').find('.ucbr-template-value').val();

					var obj = get_preview(get_widget_id(), load_widget, loading, list, item, not_found, style, no_thumb, function (data) {
						ucbr_obj.hide_loading();
						$('#ucbr-modal-message').css('width', '90%');
						ucbr_obj.show_modal_message('<iframe id="ucbr-design-preview-iframe"></iframe>');
						var html = '';
						html += '<h3><?php _e( "Loading", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
						html += '<div class="ucbr-preview-item">';
						html += data.result.loading;
						html += '</div>';
						html += '<h3><?php _e( "List", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
						html += '<div class="ucbr-preview-item">';
						html += data.result.list;
						html += '</div>';
						html += '<h3><?php _e( "Not Found", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
						html += '<div class="ucbr-preview-item">';
						html += data.result.not_found;
						html += '</div>';
						html += '<style>';
						html += 'h3{margin-bottom: 5px}';
						html += '.ucbr-preview-item{border: 4px #ccc double; margin:0 10px 10px}';
						html += '</style>';
						set_iframe_doc('ucbr-design-preview-iframe', html);
						ucbr_obj.enable_controls();
					}, function () {
						ucbr_obj.hide_modal();
						ucbr_obj.enable_controls();
					});

					ucbr_obj.show_modal(true, function () {
						obj.abort();
						ucbr_obj.hide_modal();
					});
					return false;
				});
				$('#ucbr-save-design-button').click(function () {
					ucbr_obj.disable_controls();
					var li = $('#ucbr-board').find('li');
					var values = {};
					$(li).each(function () {
						var name = $(this).data('name');
						var value = $(this).find('.ucbr-template-value').val();
						values[name] = value;
					});
					save_design_templates(get_widget_id(), values, function () {
						set_updated('<?php _e( "Saved.", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
						ucbr_obj.enable_controls();
					});
					return false;
				});

				ucbr_obj.enable_controls();
			}, function (error) {
				set_error(error);
			});
		}

		function update_design_template(tab_id, name, controles) {
			if (controles) {
				ucbr_obj.disable_controls();
			}
			var elem = $('li[data-name="' + name + '"]');
			if (elem.length <= 0) {
				if (controles) {
					ucbr_obj.enable_controls();
				}
				return;
			}
			ucbr_obj.get_design_templates({id: get_widget_id()}, function (data) {
				if (tab_id !== ucbr_obj.tab_id) {
					return;
				}
				if (!data.result) {
					set_error(data.message);
					return;
				}

				$(elem).find('.ucbr-template-value').val(data.result[name]);
				if (controles) {
					ucbr_obj.enable_controls();
					$(elem).find('.ucbr-template-value').trigger('blur');
				}
			}, function (error) {
				set_error(error);
			});
		}

		function save_design_template(id, name, value, func) {
			var d = {id: id};
			d[name] = value;
			ucbr_obj.save_design_templates(d, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function save_design_templates(id, values, func) {
			var d = {id: id};
			for (var key in values) {
				d[key] = values[key];
			}
			ucbr_obj.save_design_templates(d, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function init_design_template(id, name, func) {
			var d = {id: id, init: true};
			d[name] = true;
			ucbr_obj.save_design_templates(d, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function set_preview(tab_id) {
			if (tab_id !== ucbr_obj.tab_id) {
				return;
			}
			$('#ucbr-board').html(ucbr_obj.now_loading);
			ucbr_obj.disable_controls();
			get_widget_setting(get_widget_id(), 'no_context_mode', function (data) {
				if (tab_id !== ucbr_obj.tab_id) {
					return;
				}
				if (data.result.value) {
					ucbr_obj.enable_controls();
					var html = '';
					html += '<input type="button" value="<?php _e( "Get Preview", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-get-preview">';
					html += '<div id="ucbr-preview"></div>';
					$('#ucbr-board').html(html);

					$('#ucbr-get-preview').click(function () {
						ucbr_obj.disable_controls();
						$('#ucbr-preview').html(ucbr_obj.now_loading);
						get_widget(get_widget_id(), 0, function (data) {
							$('#ucbr-preview').html('<iframe id="ucbr-preview-iframe"></iframe>');
							set_iframe_doc('ucbr-preview-iframe', data.message);
							ucbr_obj.enable_controls();
						});
						return false;
					});
					$('#ucbr-get-preview').trigger('click');
				} else {
					get_posts(function (data) {
						if (tab_id !== ucbr_obj.tab_id) {
							return;
						}
						ucbr_obj.enable_controls();
						var html = '';
						if (data.result.length > 0) {
							html += '<select id="ucbr-select-preview-post">';
							for (var key in data.result) {
								html += '<option value="' + data.result[key].ID + '">';
								html += data.result[key].post_title;
								html += '</option>';
							}
							html += '</select>';
							html += '<br>';
							html += '<input type="button" value="<?php _e( "Get Preview", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-get-preview">';
							html += '<input type="button" value="<?php _e( "Get Posts", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-get-posts">';
							html += '<div id="ucbr-preview"></div>';
						} else {
							html += '<?php _e( "Please create post.", UCB_RECOMMEND_TEXT_DOMAIN ); ?><br>';
							html += '<a href="<?php echo $new_post_link;?>"><?php _e( "Create", UCB_RECOMMEND_TEXT_DOMAIN ); ?></a>';
						}
						$('#ucbr-board').html(html);

						$('#ucbr-get-preview').click(function () {
							ucbr_obj.disable_controls();
							var id = $('#ucbr-select-preview-post').val();
							$('#ucbr-preview').html(ucbr_obj.now_loading);
							get_widget(get_widget_id(), id, function (data) {
								$('#ucbr-preview').html('<iframe id="ucbr-preview-iframe"></iframe>');
								set_iframe_doc('ucbr-preview-iframe', data.message);
								ucbr_obj.enable_controls();
							});
							return false;
						});
						$('#ucbr-get-posts').click(function () {
							set_preview(tab_id);
							return false;
						});
						$('#ucbr-get-preview').trigger('click');
					});
				}
			});
		}

		function set_display_conditions(tab_id) {
			if (tab_id !== ucbr_obj.tab_id) {
				return;
			}
			$('#ucbr-board').html(ucbr_obj.now_loading);
			ucbr_obj.disable_controls();
			get_devices(function (data) {
				if (tab_id !== ucbr_obj.tab_id) {
					return;
				}
				ucbr_obj.enable_controls();
				var html = '';
				html += '<h3><?php _e( "Devices", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
				html += '<ul id="ucbr-devices">';
				for (var key in data.result) {
					html += '<li class="ucbr-device">';
					html += '<label for="ucbr-device-' + key + '">';
					html += '<input type="checkbox" id="ucbr-device-' + key + '" value="' + key + '">';
					html += data.result[key][0];
					html += '</label>';
					html += '</li>';
				}
				html += '<li>';
				html += '<label for="ucbr-custom-device"><?php _e( "Custom", UCB_RECOMMEND_TEXT_DOMAIN ); ?><br>';
				html += '<textarea id="ucbr-custom-device"></textarea>';
				html += '</label>';
				html += '</li>';
				html += '</ul>';
				html += '<input type="button" id="ucbr-save-device-button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>">';
				html += '<input type="button" id="ucbr-init-device-button" value="<?php _e( "Init", UCB_RECOMMEND_TEXT_DOMAIN ); ?>">';
				///	html += '<h3><?php _e( "Post", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
				$('#ucbr-board').html(html);
				$('#ucbr-devices input, #ucbr-custom-device').attr("disabled", "disabled");
				get_valid_devices(get_widget_id(), function (data) {
					for (var key in data.result.devices) {
						$('#ucbr-device-' + data.result.devices[key]).prop("checked", true);
					}
					$('#ucbr-custom-device').val(data.result.custom);
					$('#ucbr-devices input, #ucbr-custom-device').removeAttr("disabled");
					$('#ucbr-custom-device').autosize();
				});
				$('#ucbr-save-device-button').click(function () {
					var devices = [];
					$('#ucbr-devices input:checked').each(function () {
						devices.push($(this).val());
					});
					var custom = $('#ucbr-custom-device').val();
					$('#ucbr-board').html(ucbr_obj.now_loading);
					ucbr_obj.disable_controls();
					save_valid_devices(get_widget_id(), devices, custom, function () {
						set_updated('<?php _e( "Saved.", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
						set_display_conditions(tab_id);
					});
					return false;
				});
				$('#ucbr-init-device-button').click(function () {
					$('#ucbr-board').html(ucbr_obj.now_loading);
					ucbr_obj.disable_controls();
					save_valid_devices(get_widget_id(), '', '', function () {
						set_updated('<?php _e( "Initialized.", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
						set_display_conditions(tab_id);
					});
					return false;
				});
			});
		}

		function set_conditions(tab_id, selected) {
			if (tab_id !== ucbr_obj.tab_id) {
				return;
			}
			$('#ucbr-board').html(ucbr_obj.now_loading);
			ucbr_obj.disable_controls();
			var id = get_widget_uuid();
			get_condition_groups(id, function (data) {
				if (tab_id !== ucbr_obj.tab_id) {
					return;
				}
				if (id !== get_widget_uuid()) {
					return;
				}
				ucbr_obj.enable_controls();
				var html = '';
				if (data.result.length > 0) {
					html += '<select id="ucbr-select-condition-group">';
					for (var key in data.result) {
						html += '<option value="' + data.result[key].uuid + '">';
						html += data.result[key].name;
						html += '</option>';
					}
					html += '</select>';
					html += '<input type="button" value="<?php _e( "Delete", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-delete-condition-group-button">';
				}
				html += '<div id="ucbr-add-condition-group-wrap">';
				html += '<div><?php _e( "Add conditions group", UCB_RECOMMEND_TEXT_DOMAIN ); ?></div>';
				html += '<input type="text" id="ucbr-add-condition-group-name" placeholder="<?php _e( "Group name", UCB_RECOMMEND_TEXT_DOMAIN ); ?>">';
				html += '<input type="button" value="<?php _e( "Create", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-add-condition-group-button">';
				html += '<br>';
				html += get_condition_set_select();
				html += '<input type="button" value="<?php _e( "Add from template", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-add-condition-set-button">';
				html += '</div>';
				html += '<div id="ucbr-condition-group">';
				html += '</div>';
				$('#ucbr-board').html(html);

				$('#ucbr-select-condition-group').change(function () {
					var id = $(this).val();
					set_condition_group(id);
				});
				$('#ucbr-delete-condition-group-button').click(function () {
					if (!window.confirm('<?php _e( "Are you sure you want?", UCB_RECOMMEND_TEXT_DOMAIN ); ?>')) {
						return false;
					}
					var id = $('#ucbr-select-condition-group option:selected').val();
					$('#ucbr-board').html(ucbr_obj.now_loading);
					ucbr_obj.disable_controls();
					delete_condition_group(id, function () {
						set_conditions(tab_id);
					});
					return false;
				});
				$('#ucbr-add-condition-group-button').click(function () {
					var name = $('#ucbr-add-condition-group-name').val();
					if (name.length <= 0) {
						return false;
					}
					$('#ucbr-add-condition-group-name').val('');
					$('#ucbr-board').html(ucbr_obj.now_loading);
					ucbr_obj.disable_controls();
					save_condition_group('', name, function (data) {
						ucbr_obj.enable_controls();
						set_conditions(tab_id, data.result.uuid);
					});
					return false;
				});
				$('#ucbr-add-condition-set-button').click(function () {
					var slug = $('#ucbr-select-condition-set').val();
					$('#ucbr-board').html(ucbr_obj.now_loading);
					ucbr_obj.disable_controls();
					add_condition_set(get_widget_uuid(), slug, function (data) {
						set_conditions(tab_id, data.result);
					});
					return false;
				});
				if (selected) {
					var s = $('#ucbr-select-condition-group [value="' + selected + '"]');
				} else {
					var s = $('#ucbr-select-condition-group option:first');
				}
				if (s.length > 0) {
					$('#ucbr-select-condition-group').val($(s).val()).trigger('change');
				}
			});
		}

		function set_join_tables(tab_id) {
			if (tab_id !== ucbr_obj.tab_id) {
				return;
			}
			$('#ucbr-board').html(ucbr_obj.now_loading);
			ucbr_obj.disable_controls();
			var id = get_widget_uuid();
			get_join_tables(id, function (data) {
				if (id !== get_widget_uuid()) {
					return;
				}
				ucbr_obj.enable_controls();
				var html = '<ul>';
				for (var key in data.result) {
					html += '<li class="ucbr-join-table" data-uuid="' + data.result[key].uuid + '">';
					html += '<span class="ucbr-join-table-type-wrap">' + get_join_type_select(data.result[key].type) + '</span>';
					html += '<span class="ucbr-join-table-table1-wrap">' + get_table_select(data.result[key].table1) + '</span>';
					html += '<span class="ucbr-join-table-column1-wrap" data-init="' + data.result[key].column1 + '"></span>';
					html += '<span class="ucbr-join-table-table2-wrap">' + get_table_select(data.result[key].table2) + '</span>';
					html += '<span class="ucbr-join-table-column2-wrap" data-init="' + data.result[key].column2 + '"></span>';
					html += '<br>';
					html += '<input type="button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-save-join-table-button">';
					html += '<input type="button" value="<?php _e( "Delete", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-delete-join-table-button">';
					html += '</li>';
				}
				html += '</ul>';
				html += '<input type="button" value="<?php _e( "Add condition", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-add-join-table-button">';
				$('#ucbr-board').html(html);

				$('.ucbr-save-join-table-button').click(function () {
					ucbr_obj.disable_controls();
					var li = $(this).closest('li');
					var id = $(li).data('uuid');
					var type = $(li).find('.ucbr-select-join-type').val();
					var table1 = $(li).find('.ucbr-join-table-table1-wrap .ucbr-select-table').val();
					var table2 = $(li).find('.ucbr-join-table-table2-wrap .ucbr-select-table').val();
					var column1 = $(li).find('.ucbr-join-table-column1-wrap .ucbr-select-column').val();
					var column2 = $(li).find('.ucbr-join-table-column2-wrap .ucbr-select-column').val();
					save_join_table(id, get_widget_uuid(), '', type, table1, column1, table2, column2, function () {
						ucbr_obj.enable_controls();
						set_updated('<?php _e( "Saved.", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
					});
					return false;
				});
				$('.ucbr-delete-join-table-button').click(function () {
					if (!window.confirm('<?php _e( "Are you sure you want?", UCB_RECOMMEND_TEXT_DOMAIN ); ?>')) {
						return false;
					}
					ucbr_obj.disable_controls();
					var id = $(this).closest('li').data('uuid');
					$('#ucbr-board').html(ucbr_obj.now_loading);
					delete_join_table(id, function () {
						set_join_tables(tab_id);
					});
					return false;
				});
				$('#ucbr-add-join-table-button').click(function () {
					ucbr_obj.disable_controls();
					$('#ucbr-board').html(ucbr_obj.now_loading);
					save_join_table('', get_widget_uuid(), '', '', '', '', '', '', function (data) {
						ucbr_obj.enable_controls();
						set_join_tables(tab_id, data.result.uuid);
					});
					return false;
				});
				$('.ucbr-join-table-table1-wrap .ucbr-select-table').change(function () {
					table_changed(this, function (elem) {
						return $(elem).closest('.ucbr-join-table').find('.ucbr-join-table-column1-wrap');
					});
				});
				$('.ucbr-join-table-table2-wrap .ucbr-select-table').change(function () {
					table_changed(this, function (elem) {
						return $(elem).closest('.ucbr-join-table').find('.ucbr-join-table-column2-wrap');
					});
				});
				$('.ucbr-select-table').trigger('change');
			});
		}

		function set_histories(tab_id) {
			if (tab_id !== ucbr_obj.tab_id) {
				return;
			}
			$('#ucbr-board').html(ucbr_obj.now_loading);
			ucbr_obj.disable_controls();
			var id = get_widget_id();
			get_histories(id, function (data) {
				if (id !== get_widget_id()) {
					return;
				}
				ucbr_obj.enable_controls();
				var html = '';
				html += '<h3>' + data.result.diff + '</h3>';
				html += '<div id="ucbr-histories-total">' + data.result.total + '</div>';
				html += '<h3><?php echo __( 'Clicked histories', UCB_RECOMMEND_TEXT_DOMAIN );?></h3>';
				html += '<table class="widefat striped"><tr>';
				html += '<th><?php echo __( 'Date', UCB_RECOMMEND_TEXT_DOMAIN );?></th>';
				html += '<th><?php echo __( 'Post ID', UCB_RECOMMEND_TEXT_DOMAIN );?></th>';
				if (!data.result.no_context) {
					html += '<th><?php echo __( 'Context', UCB_RECOMMEND_TEXT_DOMAIN );?></th>';
				}
				html += '</tr>';
				if (data.result.data.length > 0) {
					for (var key in data.result.data) {
						html += '<tr>'
						html += '<td>' + data.result.data[key].updated_at + '</td>';
						html += '<td>' + data.result.data[key].post_id + '</td>';
						if (!data.result.no_context) {
							html += '<td>' + data.result.data[key].context + '</td>';
						}
						html += '</tr>';
					}
				} else {
					html += '<tr><td><?php echo __( 'Item not found.', UCB_RECOMMEND_TEXT_DOMAIN );?></td></tr>';
				}

				html += '<tr></table>';
				$('#ucbr-board').html(html);
			});
		}

		function set_help(tab_id) {
			if (tab_id !== ucbr_obj.tab_id) {
				return;
			}
			$('#ucbr-board').html('');
			ucbr_obj.enable_controls();
		}

		function get_condition_groups(id, func) {
			ucbr_obj.get_condition_groups({id: id}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function save_condition_group(id, name, func) {
			ucbr_obj.save_condition_group({id: id, w: get_widget_uuid(), n: name}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function delete_condition_group(id, func) {
			ucbr_obj.delete_condition_group({id: id}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function set_condition_group(id, func) {
			$('#ucbr-condition-group').html(ucbr_obj.now_loading);
			ucbr_obj.disable_controls();
			ucbr_obj.group_id = id;
			get_conditions(id, function (data) {
				if (id !== ucbr_obj.group_id) {
					return;
				}
				ucbr_obj.enable_controls();

				var html = '<ul>';
				for (var key in data.result) {
					html += '<li class="ucbr-condition" data-uuid="' + data.result[key].uuid + '">';
					html += '<span class="ucbr-select-table-wrap">' + get_table_select(data.result[key].table_name) + '</span>';
					html += '<span class="ucbr-select-column-wrap" data-init="' + data.result[key].column_name + '"></span>';
					html += '<span class="ucbr-select-verb-wrap" data-init="' + data.result[key].verb + '"></span>';
					html += '<span class="ucbr-objects-wrap">' + ucbr_obj.now_loading + '</span>';
					html += '<br>';
					html += '<input type="button" value="<?php _e( "Save", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-save-condition-button">';
					html += '<input type="button" value="<?php _e( "Delete", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-delete-condition-button">';
					html += '</li>';
					html += '<li>AND</li>';
				}
				html += '</ul>';
				html += '<input type="button" value="<?php _e( "Create", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-add-condition-button">';
				html += '<div id="ucbr-post-ids">';
				html += '</div>';
				$('#ucbr-condition-group').html(html);

				set_search_conditions_test(ucbr_obj.tab_id);
				$('.ucbr-condition').each(function () {
					var uuid = $(this).data('uuid');
					var objects = $(this).find('.ucbr-objects-wrap');
					get_objects(uuid, function (data) {
						set_objects_func(objects, data, uuid);
					});
				});
				$('#ucbr-add-condition-button').click(function () {
					ucbr_obj.disable_controls();
					$('#ucbr-condition-group').html(ucbr_obj.now_loading);
					save_condition('', ucbr_obj.group_id, '', '', '', function (data) {
						set_condition_group(ucbr_obj.group_id);
					});
					return false;
				});
				$('.ucbr-save-condition-button').click(function () {
					ucbr_obj.disable_controls();
					var li = $(this).closest('li');
					var id = $(li).data('uuid');
					var table = $(li).find('.ucbr-select-table').val();
					var column = $(li).find('.ucbr-select-column').val();
					var verb = $(li).find('.ucbr-select-verb').val();
					save_condition(id, ucbr_obj.group_id, table, column, verb, function () {
						ucbr_obj.enable_controls();
						set_updated('<?php _e( "Saved.", UCB_RECOMMEND_TEXT_DOMAIN ); ?>');
					});
					return false;
				});
				$('.ucbr-delete-condition-button').click(function () {
					if (!window.confirm('<?php _e( "Are you sure you want?", UCB_RECOMMEND_TEXT_DOMAIN ); ?>')) {
						return false;
					}
					ucbr_obj.disable_controls();
					var id = $(this).closest('li').data('uuid');
					$('#ucbr-condition-group').html(ucbr_obj.now_loading);
					delete_condition(id, function () {
						set_condition_group(ucbr_obj.group_id);
					});
					return false;
				});
				$('.ucbr-select-table').change(function () {
					table_changed(this, function (elem) {
						return $(elem).closest('.ucbr-condition').find('.ucbr-select-column-wrap');
					});
				});
				$('.ucbr-select-table').trigger('change');

				if (func) func();
			});
		}

		function set_search_conditions_test(tab_id) {
			$('#ucbr-post-ids').html(ucbr_obj.now_loading);
			get_widget_setting(get_widget_id(), 'no_context_mode', function (data) {
				if (tab_id !== ucbr_obj.tab_id) {
					return;
				}
				var html = '';
				html += '<h3><?php _e( "Search conditions test", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h3>';
				if (data.result.value) {
					html += '<input type="hidden" id="ucbr-get-post-ids-id" value="0">';
					html += '<?php _e( "Get number", UCB_RECOMMEND_TEXT_DOMAIN ); ?>: ';
					html += '<input type="number" id="ucbr-get-post-ids-number" value="<?php echo $condition_test_number;?>" min="1" max="100"><br>';
				} else {
					html += '<?php _e( "Post ID", UCB_RECOMMEND_TEXT_DOMAIN ); ?>: ';
					html += '<input type="number" id="ucbr-get-post-ids-id"><br>';
					html += '<?php _e( "Get number", UCB_RECOMMEND_TEXT_DOMAIN ); ?>: ';
					html += '<input type="number" id="ucbr-get-post-ids-number" value="<?php echo $condition_test_number;?>" min="1" max="100"><br>';
				}
				html += '<input type="button" value="<?php _e( "Get", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-get-post-ids">';
				html += '<div id="ucbr-post-ids-result"></div>';
				$('#ucbr-post-ids').html(html);

				$('#ucbr-get-post-ids').click(function () {
					var id = $('#ucbr-get-post-ids-id').val() - 0;
					if (id <= 0 && !data.result.value) {
						return;
					}
					var n = $('#ucbr-get-post-ids-number').val();
					$(this).attr("disabled", "disabled");
					$('#ucbr-post-ids-result').html(ucbr_obj.now_loading);
					get_bandits(get_widget_id(), id, n, function (data) {
						var html = '';
						if (data.result.title) {
							html += '「' + data.result.title + '」';
						}
						html += '<table>';
						html += '<tr>';
						html += '<th></th>';
						html += '<th><?php _e( "Post ID", UCB_RECOMMEND_TEXT_DOMAIN ); ?></th>';
						html += '<th><?php _e( "Post name", UCB_RECOMMEND_TEXT_DOMAIN ); ?></th>';
						html += '<th><?php _e( "Score", UCB_RECOMMEND_TEXT_DOMAIN ); ?></th>';
						if (data.result.rand) {
							html += '<th><?php _e( "Score", UCB_RECOMMEND_TEXT_DOMAIN ); ?>(rand)</th>';
						}
						html += '<th><?php _e( "Display number", UCB_RECOMMEND_TEXT_DOMAIN ); ?></th>';
						html += '<th><?php _e( "Clicked number", UCB_RECOMMEND_TEXT_DOMAIN ); ?></th>';
						html += '</tr>';
						if (data.result.list.length > 0) {
							var n = 1;
							for (var key in data.result.list) {
								html += '<tr>';
								html += '<td>' + n + '</td>';
								html += '<td>' + data.result.list[key].post_id + '</td>';
								html += '<td>' + data.result.list[key].post_title + '</td>';
								html += '<td>' + data.result.list[key].bandit + '</td>';
								if (data.result.rand) {
									html += '<td>' + data.result.list[key].score + '</td>';
								}
								html += '<td>' + data.result.list[key].n + '</td>';
								html += '<td>' + data.result.list[key].c + '</td>';
								html += '</tr>';
								n++;
							}
						} else {
							html += '<tr>';
							html += '<td colspan="7"><?php _e( "Item not found.", UCB_RECOMMEND_TEXT_DOMAIN ); ?></td>';
							html += '</tr>';
						}
						html += '</table>';
						$('#ucbr-post-ids-result').html(html);
						$('#ucbr-get-post-ids').removeAttr("disabled");
					});
				});
			});
		}

		function table_changed(elem, column_func) {
			var table = $(elem).val();
			var column = column_func(elem);
			var init = $(column).data('init');
			$(column).data('init', '');
			$(column).html(ucbr_obj.now_loading);
			get_columns(table, function (data) {
				$(column).html(get_column_select(data, init));
				$(column).find('.ucbr-select-column').change(function () {
					column_changed(this);
				});
				$(column).find('.ucbr-select-column').trigger('change');
			});
		}

		function column_changed(elem) {
			var verb = $(elem).closest('.ucbr-condition').find('.ucbr-select-verb-wrap');
			var type = $(elem).find('option:selected').data('type');
			var init = $(verb).data('init');
			$(verb).data('init', '-1');
			$(verb).html(ucbr_obj.now_loading);
			get_verbs(type, function (data) {
				if (init < 0 && ucbr_obj.prev_verb[data.result.type] !== null) {
					init = ucbr_obj.prev_verb[data.result.type];
				}
				ucbr_obj.prev_type = data.result.type;
				$(verb).html(get_verb_select(data, init));
				$(verb).find('.ucbr-select-verb').change(function () {
					ucbr_obj.prev_verb[data.result.type] = $(this).val();
				});
				$(verb).find('.ucbr-select-verb').trigger('change');
			});
		}

		function set_objects_func(elem, data, uuid) {
			set_objects(data, elem);
			$(elem).find('.ucbr-delete-object-button').click(function () {
				ucbr_obj.disable_controls();
				$(this).closest('li').find('.ucbr-delete-object').remove();
				var values = [];
				$(elem).find('.ucbr-delete-object').each(function () {
					var val = $(this).val();
					values.push(val);
				});
				$(elem).html(ucbr_obj.now_loading);
				save_objects(uuid, values, function () {
					get_objects(uuid, function (data) {
						set_objects_func(elem, data, uuid);
					});
				});
				return false;
			});
			$(elem).find('.ucbr-add-object').click(function () {
				var value = $(elem).find('.ucbr-add-object-value').val();
				if (value.length <= 0) {
					return false;
				}
				ucbr_obj.disable_controls();
				$(elem).append('<input type="hidden" value="' + value + '" class="ucbr-delete-object">');
				var values = [];
				$(elem).find('.ucbr-delete-object').each(function () {
					var val = $(this).val();
					values.push(val);
				});
				$(elem).html(ucbr_obj.now_loading);
				save_objects(uuid, values, function () {
					get_objects(uuid, function (data) {
						set_objects_func(elem, data, uuid);
					});
				});
				return false;
			});
			ucbr_obj.enable_controls();
		}

		function get_conditions(id, func) {
			return ucbr_obj.get_conditions({id: id}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function save_condition(id, group, table, column, verb, func) {
			ucbr_obj.save_condition({id: id, group: group, table: table, column: column, verb: verb}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function delete_condition(id, func) {
			ucbr_obj.delete_condition({id: id}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func)func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function add_condition_set(id, slug, func) {
			ucbr_obj.add_condition_set({w: id, s: slug}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func)func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_objects(id, func) {
			ucbr_obj.get_objects({id: id}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func)func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function save_objects(id, o, func) {
			ucbr_obj.save_objects({id: id, o: o}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func)func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function set_objects(data, elem) {
			var html = '<ul>';
			for (var key in data.result) {
				html += '<li>';
				html += '<input type="text" class="ucbr-delete-object-label" readonly="readonly" value="' + data.result[key].value + '">';
				html += '<input type="hidden" value="' + data.result[key].value + '" class="ucbr-delete-object">';
				html += '<input type="button" value="<?php _e( "Delete", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-delete-object-button">';
				html += '</li>';
				html += '<li>OR</li>';
			}
			html += '<li>';
			html += '<input type="text" class="ucbr-add-object-value" placeholder="<?php _e( "Value", UCB_RECOMMEND_TEXT_DOMAIN ); ?>">';
			html += '<input type="button" value="<?php _e( "Add", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" class="ucbr-add-object">';
			html += '</li>';
			html += '</ul>';
			$(elem).html(html);
		}

		function get_join_tables(id, func) {
			ucbr_obj.get_join_tables({id: id}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func)func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function save_join_table(id, widget, order, type, table1, column1, table2, column2, func) {
			ucbr_obj.save_join_table({
				id: id,
				widget: widget,
				order: order,
				type: type,
				table1: table1,
				column1: column1,
				table2: table2,
				column2: column2
			}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function delete_join_table(id, func) {
			ucbr_obj.delete_join_table({id: id}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_widget_setting(id, n, func) {
			ucbr_obj.get_widget_setting({id: id, n: n}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function save_widget_setting(id, n, v, func) {
			ucbr_obj.save_widget_setting({id: id, n: n, v: v}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function init_widget_setting(id, n, func) {
			ucbr_obj.save_widget_setting({id: id, n: n, v: '', init: 1}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_valid_devices(id, func) {
			ucbr_obj.get_valid_devices({id: id}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function save_valid_devices(id, d, c, func) {
			ucbr_obj.save_valid_devices({id: id, d: d, c: c}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_devices(func) {
			if (ucbr_obj.devices !== null) {
				if (func) func(ucbr_obj.devices);
				return;
			}
			ucbr_obj.get_devices({}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				ucbr_obj.devices = data;
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_verbs(type, func) {
			if (type in ucbr_obj.verbs) {
				if (func) func(ucbr_obj.verbs[type]);
				return;
			}
			ucbr_obj.get_verbs({t: type, c: 1}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				ucbr_obj.verbs[type] = data;
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_verb_select(data, selected) {
			var html = '<select class="ucbr-select-verb">';
			for (var key in data.result.verbs) {
				html += '<option value="' + key + '" data-multi="' + data.result.verbs[key][1] + '" ';
				if (key == selected) {
					html += 'selected="selected"';
				}
				html += '>';
				html += data.result.verbs[key][0];
				html += '</option>';
			}
			html += '</select>';
			return html;
		}

		function get_tables(func) {
			if (ucbr_obj.tables !== null) {
				if (func) func(ucbr_obj.tables);
				return;
			}
			ucbr_obj.get_tables({}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				ucbr_obj.tables = data;
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_table_select(selected) {
			var html = '<select class="ucbr-select-table">';
			for (var key in ucbr_obj.tables.result) {
				html += '<option value="' + ucbr_obj.tables.result[key] + '" ';
				if (ucbr_obj.tables.result[key] === selected || (undefined === selected && ucbr_obj.tables.result[key].match(/_posts$/) )) {
					html += 'selected="selected"';
				}
				html += '>';
				html += ucbr_obj.tables.result[key];
				html += '</option>';
			}
			html += '</select>';
			return html;
		}

		function get_columns(table, func) {
			if (table in ucbr_obj.columns) {
				if (func) func(ucbr_obj.columns[table]);
				return;
			}
			ucbr_obj.get_columns({t: table}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				ucbr_obj.columns[table] = data;
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_column_select(data, selected) {
			var html = '<select class="ucbr-select-column">';
			for (var key in data.result) {
				html += '<option value="' + data.result[key][0] + '" data-type="' + data.result[key][1] + '" ';
				if (data.result[key][0] === selected) {
					html += 'selected="selected"';
				}
				html += '>';
				html += data.result[key][0];
				html += '</option>';
			}
			html += '</select>';
			return html;
		}

		function get_condition_set(func) {
			if (ucbr_obj.condition_set !== null) {
				if (func) func(ucbr_obj.condition_set);
				return;
			}
			ucbr_obj.get_condition_set({}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				ucbr_obj.condition_set = data;
				if (func) func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_condition_set_select() {
			var html = '<select id="ucbr-select-condition-set">';
			for (var key in ucbr_obj.condition_set.result) {
				html += '<option value="' + key + '">';
				html += ucbr_obj.condition_set.result[key][0];
				html += '</option>';
			}
			html += '</select>';
			return html;
		}

		function get_join_types(func) {
			if (ucbr_obj.join_types !== null) {
				if (func) func(ucbr_obj.join_types);
				return;
			}
			ucbr_obj.get_join_types({}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				ucbr_obj.join_types = data;
				if (func)func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_join_type_select(selected) {
			var html = '<select class="ucbr-select-join-type">';
			for (var key in ucbr_obj.join_types.result) {
				html += '<option value="' + key + '" ';
				if (key === selected) {
					html += 'selected="selected"';
				}
				html += '>';
				html += ucbr_obj.join_types.result[key];
				html += '</option>';
			}
			html += '</select>';
			return html;
		}

		function set_updated(message) {
			$('#ucbr-message').html('<div class="updated"><ul><li><p>' + message + '</p></li></ul></div>').show();
			set_message_timer();
		}

		function set_error(message) {
			$('#ucbr-message').html('<div class="error"><ul><li><p>' + message + '</p></li></ul></div>').show();
			set_message_timer();
		}

		function set_message_timer() {
			if (ucbr_obj.message_timer) {
				clearTimeout(ucbr_obj.message_timer);
			}
			ucbr_obj.message_timer = setTimeout(function () {
				clear_message();
			}, 10000);
		}

		function clear_message() {
			$('#ucbr-message').fadeOut(400, function () {
				$('#ucbr-message').html('');
			});
			ucbr_obj.message_timer = null;
		}

		function get_widget(id, p, func) {
			ucbr_obj.widget({id: id, p: p, preview: 1}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func)func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_posts(func) {
			ucbr_obj.get_posts({}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func)func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function set_iframe_doc(iframe_id, src) {
			var elem = document.getElementById(iframe_id);
			var idoc = elem.contentWindow.document;
			idoc.open();
			idoc.write(src);
			idoc.close();

			$(window).off('resize.iframe');
			$(window).on('resize.iframe', function () {
				var top = $(window).scrollTop();
				var myF = document.getElementById(iframe_id);
				if (!myF) {
					$(window).off('resize.iframe');
					return;
				}
				var myC = myF.contentWindow.document.documentElement;
				var myH = 100;
				myF.style.height = myH + "px";
				if (document.all) {
					myH = myC.scrollHeight;
				} else {
					myH = myC.offsetHeight;
				}
				myF.style.height = (myH + 20) + "px";
				$(window).scrollTop(top);
			});
			$(window).trigger('resize');
		}

		function get_post_ids(id, p, func) {
			ucbr_obj.get_post_ids({id: id, p: p}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func)func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_bandits(id, p, n, func) {
			ucbr_obj.get_bandits({id: id, p: p, n: n}, function (data) {
				if (!data.result) {
					set_error(data.message);
					return;
				}
				if (func)func(data);
			}, function (error) {
				set_error(error);
			});
		}

		function get_preview(id, load_widget, loading, list, item, not_found, style, no_thumb, func, error_func) {
			return ucbr_obj.get_preview({
				id: id,
				load_widget: load_widget,
				loading: loading,
				list: list,
				item: item,
				not_found: not_found,
				style: style,
				no_thumb: no_thumb
			}, function (data) {
				if (!data.result) {
					set_error(data.message);
					if (error_func) error_func();
					return;
				}
				if (func)func(data);
			}, function (error) {
				set_error(error);
				if (error_func) error_func();
			});
		}

		function get_histories(id, func) {
			ucbr_obj.get_histories({id: id}, function (data) {
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
			ucbr_obj.disable_controls();
			$('#ucbr-main').html(ucbr_obj.now_loading);

			var a = false, b = false, c = false;
			get_tables(function () {
				a = true;
				if (b && c) {
					initialize();
				}
			});
			get_condition_set(function () {
				b = true;
				if (a && c) {
					initialize();
				}
			});
			get_join_types(function () {
				c = true;
				if (a && b) {
					initialize();
				}
			});
		});
	})(jQuery);
</script>
