<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Design extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	protected function __construct()
	{
		$this->register();
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Design();
		}
		return self::$_instance;
	}

	private function register()
	{
		add_filter( $this->get_filter_prefix() . 'load_widget_template', function ( $template, $id ) {
			global $ucbr_option;
			return $ucbr_option->get( 'load_widget_template-' . $id, $template );
		}, 10, 2 );

		add_filter( $this->get_filter_prefix() . 'list_template', function ( $template, $id ) {
			global $ucbr_option;
			return $ucbr_option->get( 'list_template-' . $id, $template );
		}, 10, 2 );

		add_filter( $this->get_filter_prefix() . 'item_template', function ( $template, $id ) {
			global $ucbr_option;
			return $ucbr_option->get( 'item_template-' . $id, $template );
		}, 10, 2 );

		add_filter( $this->get_filter_prefix() . 'not_found_template', function ( $template, $id ) {
			global $ucbr_option;
			return $ucbr_option->get( 'not_found_template-' . $id, $template );
		}, 10, 2 );

		add_filter( $this->get_filter_prefix() . 'no_thumb_image', function ( $image, $id ) {
			global $ucbr_option;
			return $ucbr_option->get( 'no_thumb_image-' . $id, $image );
		}, 10, 2 );

		add_filter( $this->get_filter_prefix() . 'loading_image', function ( $image, $id ) {
			global $ucbr_option;
			return $ucbr_option->get( 'loading_image-' . $id, $image );
		}, 10, 2 );

		add_filter( $this->get_filter_prefix() . 'style_template', function ( $template, $id ) {
			global $ucbr_option;
			return $ucbr_option->get( 'style_template-' . $id, $template );
		}, 10, 2 );

		add_filter( $this->get_filter_prefix() . 'valid_devices', function ( $devices, $id ) {
			global $ucbr_option;
			return $ucbr_option->get( 'valid_devices-' . $id, $devices );
		}, 10, 2 );

		add_filter( $this->get_filter_prefix() . 'custom_valid_device', function ( $device, $id ) {
			global $ucbr_option;
			return $ucbr_option->get( 'custom_valid_device-' . $id, $device );
		}, 10, 2 );
	}

	public function get_wrap_class( $id )
	{
		return $this->apply_filters( 'widget_warp_class', 'ucbr-widget-' . $id, $id );
	}

	public function get_load_widget_template( $id )
	{
		return $this->apply_filters( 'load_widget_template', $this->view( 'load-widget-template', false, array( 'id' => $id ) ), $id );
	}

	public function get_list_template( $id )
	{
		return $this->apply_filters( 'list_template', $this->view( 'list-template', false, array( 'id' => $id ) ), $id );
	}

	public function get_item_template( $id )
	{
		return $this->apply_filters( 'item_template', $this->view( 'item-template', false, array( 'id' => $id ) ), $id );
	}

	public function get_not_found_template( $id )
	{
		return $this->apply_filters( 'not_found_template', $this->view( 'not-found-template', false, array( 'id' => $id ) ), $id );
	}

	public function get_no_thumb_img( $id )
	{
		return $this->apply_filters( 'no_thumb_image', UCB_RECOMMEND_IMG_URL . '/' . UCB_RECOMMEND_NO_THUMB_IMAGE, $id );
	}

	public function get_loading_img( $id )
	{
		return $this->apply_filters( 'loading_image', UCB_RECOMMEND_IMG_URL . '/' . UCB_RECOMMEND_LOADING_IMAGE, $id );
	}

	public function get_style_template( $id )
	{
		return $this->apply_filters( 'style_template', $this->view( 'style-template', false, array( 'id' => $id ) ), $id );
	}

	public function get_valid_devices( $id )
	{
		$ret = unserialize( $this->apply_filters( 'valid_devices', UCB_RECOMMEND_VALID_DEVICES, $id ) );
		$ret = !is_array( $ret ) ? array() : $ret;
		return $ret;
	}

	public function get_custom_valid_device( $id )
	{
		return $this->apply_filters( 'custom_valid_device', UCB_RECOMMEND_CUSTOM_VALID_DEVICE, $id );
	}

	public function set_load_widget_template( $id, $template )
	{
		global $ucbr_option;
		$ucbr_option->set( 'load_widget_template-' . $id, $template );
	}

	public function set_list_template( $id, $template )
	{
		global $ucbr_option;
		$ucbr_option->set( 'list_template-' . $id, $template );
	}

	public function set_item_template( $id, $template )
	{
		global $ucbr_option;
		$ucbr_option->set( 'item_template-' . $id, $template );
	}

	public function set_not_found_template( $id, $template )
	{
		global $ucbr_option;
		$ucbr_option->set( 'not_found_template-' . $id, $template );
	}

	public function set_no_thumb_img( $id, $image )
	{
		global $ucbr_option;
		$ucbr_option->set( 'no_thumb_image-' . $id, $image );
	}

	public function set_loading_img( $id, $image )
	{
		global $ucbr_option;
		$ucbr_option->set( 'loading_image-' . $id, $image );
	}

	public function set_style_template( $id, $template )
	{
		global $ucbr_option;
		$ucbr_option->set( 'style_template-' . $id, $template );
	}

	public function set_valid_devices( $id, $devices )
	{
		global $ucbr_option;
		$ucbr_option->set( 'valid_devices-' . $id, serialize( $devices ) );
	}

	public function set_custom_valid_device( $id, $device )
	{
		global $ucbr_option;
		$ucbr_option->set( 'custom_valid_device-' . $id, $device );
	}

	public function delete_load_widget_template( $id )
	{
		global $ucbr_option;
		$ucbr_option->delete( 'load_widget_template-' . $id );
	}

	public function delete_list_template( $id )
	{
		global $ucbr_option;
		$ucbr_option->delete( 'list_template-' . $id );
	}

	public function delete_item_template( $id )
	{
		global $ucbr_option;
		$ucbr_option->delete( 'item_template-' . $id );
	}

	public function delete_not_found_template( $id )
	{
		global $ucbr_option;
		$ucbr_option->delete( 'not_found_template-' . $id );
	}

	public function delete_no_thumb_img( $id )
	{
		global $ucbr_option;
		$ucbr_option->delete( 'no_thumb_image-' . $id );
	}

	public function delete_loading_img( $id )
	{
		global $ucbr_option;
		$ucbr_option->delete( 'loading_image-' . $id );
	}

	public function delete_style_template( $id )
	{
		global $ucbr_option;
		$ucbr_option->delete( 'style_template-' . $id );
	}

	public function delete_valid_devices( $id )
	{
		global $ucbr_option;
		$ucbr_option->delete( 'valid_devices-' . $id );
	}

	public function delete_custom_valid_device( $id )
	{
		global $ucbr_option;
		$ucbr_option->delete( 'custom_valid_device-' . $id );
	}

	public function delete_all( $id )
	{
		$this->delete_load_widget_template( $id );
		$this->delete_list_template( $id );
		$this->delete_item_template( $id );
		$this->delete_not_found_template( $id );
		$this->delete_no_thumb_img( $id );
		$this->delete_loading_img( $id );
		$this->delete_style_template( $id );
		$this->delete_valid_devices( $id );
		$this->delete_custom_valid_device( $id );
	}

	public function parse_load_widget_template( $id )
	{
		return $this->parse_load_widget_template2( $id );
	}

	public function parse_load_widget_template2( $id, $loading_image = null, $load_widget_template = null )
	{
		if ( is_null( $loading_image ) ) {
			$loading_image = $this->get_loading_img( $id );
		}
		if ( is_null( $load_widget_template ) ) {
			$load_widget_template = $this->get_load_widget_template( $id );
		}
		$template = str_replace( '{$loading_image}', $loading_image, $load_widget_template );
		$template = '<div class="ucbr-widget-load ' . esc_attr( $this->get_wrap_class( $id ) ) . '" data-id="' . $id . '">' . $template . '</div>';
		return $template;
	}

	public function parse_template( $id, $post, $post_id, $data, $registered, $preview = false )
	{
		return $this->parse_template2( $id, $post, $post_id, $data, $registered, null, null, null, null, null, $preview );
	}

	public function parse_template2( $id, $post, $post_id, $data, $registered, $list_template, $not_found_template, $item_template, $style_template, $no_thumb, $preview = false )
	{
		$template = $this->parse_list_template( $id, $post, $post_id, $this->parse_items_template( $id, $data, $registered, $item_template, $no_thumb, $preview ), $list_template, $not_found_template, $preview );
		if ( $preview ) {
			$template = '<div class="' . esc_attr( $this->get_wrap_class( $id ) ) . '">' . $template . '</div>';

			global $ucbr_minify;
			$template .= '<style>' . $ucbr_minify->minify_css( $this->parse_style_template( $id, $style_template ) ) . '</style>';
		}
		return $template;
	}

	public function parse_list_template( $id, $post, $post_id, $items_html, $list_template = null, $not_found_template = null, $preview = false )
	{
		add_filter( 'the_content', array( $this, 'the_content' ) );
		//	add_filter( 'get_the_excerpt', array( $this, 'get_the_excerpt' ) );
		if ( $items_html ) {
			if ( is_null( $list_template ) ) {
				$html = $this->get_list_template( $id );
			} else {
				$html = $list_template;
			}
		} else {
			if ( is_null( $not_found_template ) ) {
				$html = $this->get_not_found_template( $id );
			} else {
				$html = $not_found_template;
			}
		}
		if ( $post ) {
			if ( false !== strpos( $html, '{$post->post_content}' ) ) {
				$html = str_replace( '{$post->post_content}', apply_filters( 'the_content', get_the_content( $post_id ) ), $html );
			}
			if ( false !== strpos( $html, '{$post->post_excerpt}' ) ) {
				$html = str_replace( '{$post->post_excerpt}', $this->get_the_excerpt( $post ), $html );
			}
			foreach ( $post as $key => $value ) {
				if ( 'post_content' === $key || 'post_excerpt' === $key ) {
					continue;
				}
				$html = str_replace( '{$post->' . $key . '}', esc_attr( $value ), $html );
			}
		}
		if ( $preview ) {
			$html = str_replace( '{$url}', 'javascript:void(0)', $html );
		} else {
			$html = str_replace( '{$url}', get_permalink( $post_id ), $html );
		}

		if ( $items_html ) {
			$html = str_replace( '{$list}', $items_html, $html );
		}
		remove_filter( 'the_content', array( $this, 'the_content' ) );
		//	remove_filter( 'get_the_excerpt', array( $this, 'get_the_excerpt' ) );
		return $html;
	}

	public function parse_items_template( $id, $data, $registered, $item_template = null, $no_thumb = null, $preview = false )
	{
		add_filter( 'the_content', array( $this, 'the_content' ) );
		//add_filter( 'get_the_excerpt', array( $this, 'get_the_excerpt' ) );
		$content_autop = has_filter( 'the_content', 'wpautop' );
		if ( false !== $content_autop ) {
			remove_filter( 'the_content', 'wpautop', $content_autop );
		}
		if ( is_null( $item_template ) ) {
			$item_template = $this->get_item_template( $id );
		}
		if ( is_null( $no_thumb ) ) {
			$no_thumb = $this->get_no_thumb_img( $id );
		}
		global $ucbr_access;
		$tmp_post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

		$items_html = '';
		foreach ( $data as $d ) {
			if ( $d instanceof \WP_Post ) {
				$item_post = $d;
				$d = array( 'n' => 1, 'c' => 1, 'bandit' => 1 );
				$id = 0;
			} else {
				$id = $d['post_id'];
				$item_post = get_post( $id );
				if ( is_null( $item_post ) ) {
					continue;
				}
			}
			$GLOBALS['post'] = $item_post;
			setup_postdata( $item_post );

			$bandit = $d['bandit'];
			$n = $d['n'];
			$c = $d['c'];

			$item_html = $item_template;

			if ( false !== strpos( $item_html, '{$post->post_content}' ) ) {
				$item_html = str_replace( '{$post->post_content}', apply_filters( 'the_content', $item_post->post_content ), $item_html );
			}
			if ( false !== strpos( $item_html, '{$post->post_excerpt}' ) ) {
				$item_html = str_replace( '{$post->post_excerpt}', $this->get_the_excerpt( $item_post ), $item_html );
			}
			foreach ( $item_post as $key => $value ) {
				if ( 'post_content' === $key || 'post_excerpt' === $key ) {
					continue;
				}
				$item_html = str_replace( '{$post->' . $key . '}', esc_attr( $value ), $item_html );
			}

			if ( has_post_thumbnail( $id ) ) {
				$image_id = get_post_thumbnail_id( $id );
			} else {
				$image_id = false;
			}
			if ( preg_match_all( '~{\$thumbnail({(.+?)})?}~', $item_html, $matches ) ) {
				$num = count( $matches[0] );
				for ( $i = 0; $i < $num; $i++ ) {
					if ( $image_id ) {
						$size = $matches[2][$i];
						if ( '' === $size ) {
							$size = 'thumbnail';
						}
						$image = wp_get_attachment_image_src( $image_id, $size );
						if ( $image[0] ) {
							$thumb = $image[0];
						} else {
							$thumb = $no_thumb;
						}
					} else {
						$thumb = $no_thumb;
					}
					$item_html = str_replace( $matches[0][$i], esc_attr( $thumb ), $item_html );
				}
			}

			$item_html = str_replace( '{$bandit}', $bandit, $item_html );
			$item_html = str_replace( '{$n}', $n, $item_html );
			$item_html = str_replace( '{$c}', $c, $item_html );

			if ( preg_match_all( '~{\$url({(\d+?)})?}~', $item_html, $matches ) ) {
				if ( is_array( $registered ) && array_key_exists( $id, $registered ) ) {
					$key = $registered[$id][1];
				} else {
					$key = '';
				}
				$num = count( $matches[0] );
				for ( $i = 0; $i < $num; $i++ ) {
					if ( $preview ) {
						$url = 'javascript:void(0)';
					} else {
						$index = $matches[2][$i];
						if ( '' === $index ) {
							$index = 1;
						} else {
							$index -= 0;
						}
						$url = $ucbr_access->get_test_url( $key, $id, $index );
					}
					$item_html = str_replace( $matches[0][$i], $url, $item_html );
				}
			}

			$items_html .= $item_html;
		}
		remove_filter( 'the_content', array( $this, 'the_content' ) );
		//remove_filter( 'get_the_excerpt', array( $this, 'get_the_excerpt' ) );
		if ( false !== $content_autop ) {
			add_filter( 'the_content', 'wpautop', $content_autop );
		}
		if ( is_null( $tmp_post ) ) {
			unset( $GLOBALS['post'] );
			setup_postdata( null );
		} else {
			$GLOBALS['post'] = $tmp_post;
			setup_postdata( $tmp_post );
		}
		return $items_html;
	}

	public function parse_style_template( $id, $style_template = null )
	{
		global $ucbr_minify;
		if ( is_null( $style_template ) ) {
			$style_template = $this->get_style_template( $id );
		}
		$style_template = preg_replace( '/<\s*\/?style\s*>/', '', $style_template );
		$style_template = '.' . $this->get_wrap_class( $id ) . '{' . $style_template . '}';
		$style_template = $ucbr_minify->scss_to_css( $style_template );
		return $style_template;
	}

	public function the_content( $content )
	{
		return nl2br( $content );
	}

	//	public function get_the_excerpt( $excerpt )
	//	{
	////		$excerpt = strip_tags( $excerpt );
	////		$excerpt = strip_shortcodes( $excerpt );
	//		if ( "" === $excerpt ) {
	//			return " ";
	//		}
	//		//return nl2br( $excerpt );
	//		return $excerpt;
	//	}

	private function get_the_excerpt( $post )
	{
		if ( '' !== $post->post_excerpt ) {
			return $post->post_excerpt;
		}
		if ( '' === $post->post_content ) {
			return '';
		}
		$text = strip_shortcodes( $post->post_content );
	//	$text = apply_filters( 'the_content', $text );
		$text = str_replace(']]>', ']]&gt;', $text);
		$excerpt_length = apply_filters( 'excerpt_length', 55 );
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
		return $text;
	}

}

$GLOBALS['ucbr_design'] = UCBRecommend_Design::get_instance();
