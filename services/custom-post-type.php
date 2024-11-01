<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_CustomPostType extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	protected function __construct()
	{
		$this->register();
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_CustomPostType();
		}
		return self::$_instance;
	}

	public function get_post_type()
	{
		return $this->apply_filters( 'design_post_type', 'ucbr_design' );
	}

	public function get_taxonomy()
	{
		return $this->apply_filters( 'design_taxonomy', 'ucbr_category' );
	}

	private function register()
	{
		$support = array_keys( get_all_post_type_supports( 'post' ) );
		foreach ( $support as $k => $v ) {
			if ( 'trackbacks' === $v || 'comments' === $v || 'page-attributes' === $v || 'post-formats' === $v ) {
				unset( $support[$k] );
			}
		}
		register_post_type(
			$this->get_post_type(),
			$this->apply_filters(
				'register_post_args',
				array(
					'labels' => array(
						'name' => __( 'Design Parts', UCB_RECOMMEND_TEXT_DOMAIN ),
						'singular_name' => __( 'Design Parts', UCB_RECOMMEND_TEXT_DOMAIN ),
						'add_new' => __( 'Add', UCB_RECOMMEND_TEXT_DOMAIN ),
						'add_new_item' => __( 'Add Design Parts', UCB_RECOMMEND_TEXT_DOMAIN ),
						'edit' => __( 'Edit', UCB_RECOMMEND_TEXT_DOMAIN ),
						'edit_item' => __( 'Edit Design Parts', UCB_RECOMMEND_TEXT_DOMAIN ),
						'new_item' => __( 'New Design Parts', UCB_RECOMMEND_TEXT_DOMAIN ),
						'view' => __( 'View', UCB_RECOMMEND_TEXT_DOMAIN ),
						'view_item' => __( 'View Design Parts', UCB_RECOMMEND_TEXT_DOMAIN ),
						'search_items' => __( 'Search Design Parts', UCB_RECOMMEND_TEXT_DOMAIN ),
						'not_found' => __( 'Item not found.', UCB_RECOMMEND_TEXT_DOMAIN ),
						'not_found_in_trash' => __( 'Item not found in trash.', UCB_RECOMMEND_TEXT_DOMAIN ),
						'featured_image' => __( 'Featured Image', UCB_RECOMMEND_TEXT_DOMAIN ),
						'set_featured_image' => __( 'Set featured image', UCB_RECOMMEND_TEXT_DOMAIN )
					),
					'public' => true,
					'show_in_nav_menus' => false,
					'show_ui' => true,
					'show_in_menu' => 'ucbr-setting',
					'publicly_queryable' => false,
					'exclude_from_search' => true,
					'hierarchical' => false,
					'menu_position' => 99,
					'capability_type' => 'post',
					'menu_icon' => null,
					'query_var' => false,
					'rewrite' => false,
					'has_archive' => false,
					'supports' => $support,
				)
			)
		);

		register_taxonomy(
			$this->get_taxonomy(),
			$this->get_post_type(),
			$this->apply_filters(
				'register_taxonomy_args',
				array(
					'labels' => array(
						'name' => __( 'Tags', UCB_RECOMMEND_TEXT_DOMAIN ),
						'singular_name' => __( 'Tag', UCB_RECOMMEND_TEXT_DOMAIN ),
						'separate_items_with_commas' => null,
						'add_or_remove_items' => null,
						'choose_from_most_used' => __( 'Choose from the most used tags', UCB_RECOMMEND_TEXT_DOMAIN ),
					),
					'hierarchical' => false,
					'query_var' => false,
					'rewrite' => false,
					'public' => false,
					'show_ui' => true,
					'show_admin_column' => true,
				)
			)
		);

		add_action( 'add_meta_boxes_' . $this->get_post_type(), function () {
			add_meta_box( 'ucbr_resirect_url', __( 'Redirect URL', UCB_RECOMMEND_TEXT_DOMAIN ), function ( $post ) {
				$this->redirect_url_meta_box( $post );
			}, $this->get_post_type(), 'side', 'low' );
		} );
		add_action( 'save_post_' . $this->get_post_type(), function ( $post_id ) {
			$this->save_url( $post_id );
		} );

		add_action( 'admin_head-edit.php', function () {
			if ( !isset( $_GET['post_type'] ) || $this->get_post_type() !== $_GET['post_type'] ) {
				return;
			}

			add_filter( 'manage_' . $this->get_post_type() . '_posts_columns', function ( $columns ) {
				$columns['ucbr_redirect_url'] = __( 'Redirect URL', UCB_RECOMMEND_TEXT_DOMAIN );
				return $columns;
			} );
			add_action( 'manage_' . $this->get_post_type() . '_posts_custom_column', function ( $column_name, $post_id ) {
				if ( 'ucbr_redirect_url' !== $column_name ) {
					return;
				}
				$urls = $this->get_redirect_urls( $post_id );
				$br = '';
				foreach ( $urls as $k => $url ) {
					$permalink = $this->parse_url( $url );
					if ( false == $permalink ) {
						$permalink = home_url();
					}
					echo $br . ( $k + 1 ) . '. <a href="' . esc_attr( $permalink ) . '" target="_blank">' . esc_html( $permalink ) . '</a>';
					$br = '<br>';
				}
			}, 10, 2 );
		} );
	}

	private function redirect_url_meta_box( $post )
	{
		wp_nonce_field( 'ucbr_redirect_url_meta_box', 'ucbr_redirect_url_meta_box_nonce' );

		global $ucbr_post;
		$urls = $ucbr_post->get( 'redirect_url', $post->ID );
		$urls = @unserialize( $urls );
		if ( !is_array( $urls ) || count( $urls ) <= 0 ) {
			$urls = array( '' );
		}
		$urls = array_values( $urls );
		$delete = esc_attr( __( 'Delete', UCB_RECOMMEND_TEXT_DOMAIN ) );

		echo '<div id="ucbr-redirect-urls">';
		echo __( 'Please Input URL or Post ID.', UCB_RECOMMEND_TEXT_DOMAIN );
		foreach ( $urls as $index => $url ) {
			$this->set_url_input( $index, $url, $delete );
		}
		$add = esc_attr( __( 'Add', UCB_RECOMMEND_TEXT_DOMAIN ) );
		echo <<< EOS
		</div>
		<input type="button" id="ucbr-add-redirect-url-button" class="button" value="{$add}">
		<div style="clear:both"></div>
<style>
	#ucbr-add-redirect-url-button{
		float:right;
		margin:0 10px 10px;
	}
	.ucbr-error{
		color:red;
	}
	.ucbr-redirect-url{
		margin-bottom: 5px;
	}
</style>
<script>
(function($){
	$(function(){
		$('.ucbr-delete-redirect-url-button').click(function(){
			$(this).closest('div').remove();
			return false;
		});
		$('#ucbr-add-redirect-url-button').click(function(){
			var html = '<div class="ucbr-redirect-url">';
			html += '<input type="text" id="ucbr_redirect_url_value" name="ucbr_redirect_url_value[]" value="">';
			html += '<input type="button" value="{$delete}" class="ucbr-delete-redirect-url-button button">';
			html += '</div>';
			$(html).appendTo('#ucbr-redirect-urls').find('.ucbr-delete-redirect-url-button').click(function(){
				$(this).closest('div').remove();
				return false;
			});
			$('#ucbr-redirect-urls').append('');
			return false;
		});
	});
})(jQuery);
</script>
EOS;
	}

	private function parse_url( $url )
	{
		if ( is_numeric( $url ) && is_int( $url - 0 ) ) {
			$post = get_post( $url );
			if ( is_null( $post ) ) {
				return false;
			} else {
				return get_permalink( $url );
			}
		} else {
			if ( empty( $url ) ) {
				return home_url();
			} else {
				return $url;
			}
		}
	}

	private function set_url_input( $index, $url, $delete )
	{
		echo '<div class="ucbr-redirect-url">';
		echo ( $index + 1 ) . '. ';
		$permalink = $this->parse_url( $url );
		if ( false === $permalink ) {
			$permalink = esc_html( home_url() );
			echo <<< EOS
<input type="text" id="ucbr_redirect_url_value" name="ucbr_redirect_url_value[]" value="{$url}">
<input type="button" value="{$delete}" class="ucbr-delete-redirect-url-button button"><br>
<span class="ucbr-error">Invalid Post ID</span><br>
({$permalink})
EOS;
		} else {
			$permalink = esc_html( $permalink );
			echo <<< EOS
<input type="text" id="ucbr_redirect_url_value" name="ucbr_redirect_url_value[]" value="{$url}">
<input type="button" value="{$delete}" class="ucbr-delete-redirect-url-button button"><br>
({$permalink})
EOS;
		}
		echo '</div>';
	}

	private function save_url( $post_id )
	{
		if ( !isset( $_POST['ucbr_redirect_url_meta_box_nonce'] ) ) {
			return;
		}

		if ( !wp_verify_nonce( $_POST['ucbr_redirect_url_meta_box_nonce'], 'ucbr_redirect_url_meta_box' ) ) {
			return;
		}

		global $ucbr_post;
		if ( !isset( $_POST['ucbr_redirect_url_value'] ) || !is_array( $_POST['ucbr_redirect_url_value'] ) ) {
			$ucbr_post->set( $post_id, 'redirect_url', serialize( array( '' ) ) );
			return;
		}
		$ucbr_post->set( $post_id, 'redirect_url', serialize( $_POST['ucbr_redirect_url_value'] ) );
	}

	public function get_redirect_urls( $id )
	{
		$post = get_post( $id );
		if ( is_null( $post ) ) {
			return array( home_url() );
		}
		if ( $this->get_post_type() !== $post->post_type ) {
			return array( get_permalink( $post->ID ) );
		}

		global $ucbr_post;
		$urls = $ucbr_post->get( 'redirect_url', $post->ID );
		$urls = @unserialize( $urls );
		if ( !is_array( $urls ) || count( $urls ) <= 0 ) {
			return array( home_url() );
		}

		return array_values( $urls );
	}

	public function get_redirect_url( $id, $index = 1 )
	{
		if ( $index > 0 ) {
			$index--;
		}

		$urls = $this->get_redirect_urls( $id );
		if ( $index < 0 || $index >= count( $urls ) ) {
			return home_url();
		}

		$url = $urls[$index];
		if ( is_numeric( $url ) && is_int( $url - 0 ) ) {
			$post = get_post( $url );
			if ( is_null( $post ) ) {
				return home_url();
			} else {
				return get_permalink( $post->ID );
			}
		} else {
			if ( empty( $url ) ) {
				return home_url();
			} else {
				return $url;
			}
		}
	}
}

$GLOBALS['ucbr_custom_post_type'] = UCBRecommend_CustomPostType::get_instance();
