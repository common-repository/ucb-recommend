<?php
namespace UCBRecommendController;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Contoller_Loader extends \UCBRecommendBase\UCBRecommend_Base_Class
{
	private static $_instance = null;

	private $errors   = array();
	private $messages = array();

	private function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_menu' ), 9 );
		add_action( "admin_notices", array( $this, "admin_notice" ) );

		add_action( 'init', array( $this, 'register_assets' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_global_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_global_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Contoller_Loader();
		}
		return self::$_instance;
	}

	public function load()
	{
		if ( isset( $_GET["page"] ) && ( false !== strpos( $_GET["page"], "ucbr-" ) ) ) {
			$slug = str_replace( "ucbr-", "", $_GET["page"] );
			echo '<div class="wrap ucbr-wrap">';
			echo "<div class=\"icon32 icon32-{$slug}\"><br /></div>";
			if ( isset( $GLOBALS[UCBRecommend_Controller_Base::get_prefix() . $slug] ) ) {
				$obj = $GLOBALS[UCBRecommend_Controller_Base::get_prefix() . $slug];
				if ( method_exists( $obj, 'get_capability' ) && is_callable( array( $obj, 'get_capability' ) ) &&
					method_exists( $obj, 'load' ) && is_callable( array( $obj, 'load' ) )
				) {
					global $ucbr_user;
					if ( $ucbr_user->user_can( $obj->get_capability() ) ) {
						global $ucbr_minify;
						$ucbr_minify->register_css(
							<<< EOS
						<style>
							#ucbr-main-contents input[type="button"],
							#ucbr-main-contents .ucbr-button {
								min-width: 100px;
								border: solid 2px #727272;
								box-shadow: #aaa 3px 3px 2px 2px;
								cursor: pointer;
								padding: 5px 30px;
								margin: 10px 5px;
								height: auto;
							}
						</style>
EOS
						);
						?>
						<div id="ucbr-main-contents">
						<?php
						$obj->load();
						?></div><?php
					} else {
						echo '<h2>Error</h2><div class="error"><p>' . __( "forbidden." ) . '</p></div>';
					}
				} else {
					echo '<h2>Error</h2><div class="error"><p>' . __( "page not found." ) . '</p></div>';
				}
			} else {
				echo '<h2>Error</h2><div class="error"><p>' . __( "page not found." ) . '</p></div>';
			}
			echo "</div>\n<!-- .wrap ends -->";
		}
	}

	public function add_menu()
	{
		$capability = $this->apply_filters( 'menu_capability', UCB_RECOMMEND_ADMIN_CAPABILITY, null );
		global $ucbr_user;
		if ( !$ucbr_user->user_can( $capability ) ) {
			return;
		}

		$check = false;
		foreach ( scandir( UCB_RECOMMEND_CONTROLLERS_DIR ) as $file ) {
			if ( preg_match( "/^[^\\.].*\\.php$/", $file ) ) {
				require_once UCB_RECOMMEND_CONTROLLERS_DIR . DIRECTORY_SEPARATOR . $file;
				if ( isset( $GLOBALS[UCBRecommend_Controller_Base::get_name( $file )] ) ) {
					$obj = $GLOBALS[UCBRecommend_Controller_Base::get_name( $file )];
					if ( method_exists( $obj, 'setup' ) && is_callable( array( $obj, 'setup' ) ) &&
						method_exists( $obj, 'get_capability' ) && is_callable( array( $obj, 'get_capability' ) ) &&
						method_exists( $obj, 'get_page_title' ) && is_callable( array( $obj, 'get_page_title' ) ) &&
						method_exists( $obj, 'get_menu_name' ) && is_callable( array( $obj, 'get_menu_name' ) )
					) {
						if ( $ucbr_user->user_can( $this->apply_filters( 'menu_capability', $obj->get_capability(), $obj->get_menu_name() ) ) ) {
							$check = true;
							break;
						}
					}
				}
			}
		}
		if ( $check ) {
			$icon = $this->apply_filters( "menu_image", UCB_RECOMMEND_IMG_DIR . DIRECTORY_SEPARATOR . "logo.png" );
			if ( file_exists( $icon ) ) {
				$icon = $this->dir2path( $icon );
			} elseif ( !preg_match( '~^(https?:)?//~', $icon ) ) {
				$icon = "";
			}
			add_menu_page(
				$this->apply_filters( "page_title", UCB_RECOMMEND_PLUGIN_NAME, null ),
				$this->apply_filters( "menu_title", UCB_RECOMMEND_PLUGIN_NAME, null ),
				$capability,
				"ucbr-setting",
				array( $this, "load" ),
				$icon,
				$this->apply_filters( "menu_position", 100 )
			);
			add_filter( 'plugin_action_links_' . UCB_RECOMMEND_PLUGIN_BASE_NAME, array( $this, "plugin_action_links" ) );
			foreach ( scandir( UCB_RECOMMEND_CONTROLLERS_DIR ) as $file ) {
				if ( preg_match( "/^[^\\.].*\\.php$/", $file ) ) {
					require_once UCB_RECOMMEND_CONTROLLERS_DIR . DIRECTORY_SEPARATOR . $file;
					if ( isset( $GLOBALS[UCBRecommend_Controller_Base::get_name( $file )] ) ) {
						$obj = $GLOBALS[UCBRecommend_Controller_Base::get_name( $file )];
						if ( method_exists( $obj, 'setup' ) && is_callable( array( $obj, 'setup' ) ) &&
							method_exists( $obj, 'get_capability' ) && is_callable( array( $obj, 'get_capability' ) ) &&
							method_exists( $obj, 'get_page_title' ) && is_callable( array( $obj, 'get_page_title' ) ) &&
							method_exists( $obj, 'get_menu_name' ) && is_callable( array( $obj, 'get_menu_name' ) )
						) {
							if ( $ucbr_user->user_can( $this->apply_filters( 'menu_capability', $obj->get_capability(), $obj->get_menu_name() ) ) ) {
								$obj->setup();
								$page = UCBRecommend_Controller_Base::get_page( $file );
								add_submenu_page(
									"ucbr-setting",
									$this->apply_filters( "page_title", $obj->get_page_title(), $page ),
									$this->apply_filters( "menu_title", $obj->get_menu_name(), $page ),
									$capability,
									$page,
									array( $this, "load" )
								);
							}
						}
					}
				}
			}
		}
	}

	public function plugin_action_links( $links )
	{
		$settings_link = '<a href="' . menu_page_url( 'ucbr-setting', false ) . '">' . esc_html( __( "Setting", UCB_RECOMMEND_TEXT_DOMAIN ) ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Register assets
	 */
	public function register_assets()
	{

	}

	/**
	 * Enqueue assets for public screen
	 */
	public function enqueue_global_assets()
	{

	}

	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_admin_assets()
	{

	}

	public function admin_notice()
	{
		global $ucbr_user;
		if ( !$ucbr_user->user_can() )
			return;
		if ( count( $this->errors ) > 0 ) {
			?>
			<div class="error ucbr-admin-message">
				<ul>
					<?php foreach ( $this->errors as $m ): ?>
						<li><p><?php echo $m; ?></p></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
		}
		if ( count( $this->messages ) > 0 ) {
			?>
			<div class="updated ucbr-admin-message">
				<ul>
					<?php foreach ( $this->messages as $m ): ?>
						<li><p><?php echo $m; ?></p></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
		}
	}

	public function add_error( $error )
	{
		$this->errors[] = $error;
	}

	public function add_message( $message )
	{
		$this->messages[] = $message;
	}
}

$GLOBALS['ucbr_controller'] = UCBRecommend_Contoller_Loader::get_instance();
