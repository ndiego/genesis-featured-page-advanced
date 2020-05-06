<?php
/**
* Plugin Name: 		Genesis Featured Page Advanced
* Plugin URI: 		http://www.outermost.co/
* Description: 		Adds an enhanced version of the Genesis - Featured Page widget. The Genesis Framework 2.0+ is required.
* Author: 			Nick Diego
* Author URI: 		https://www.nickdiego.com
* Version: 			2.0.0
* Text Domain: 		genesis-featured-page-advanced
* Domain Path: 		/languages
* Tested up to: 	5.4
* License: 			GPLv2
*
* Genesis Featured Page Advanced is free software: you can redistribute it and/or
* modify it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 2 of the License, or
* any later version.
*
* You should have received a copy of the GNU General Public License
* along with tGenesis Featured Page Advanced. If not, see <http://www.gnu.org/licenses/>.
*
* @package GenesisFeaturedPageAdvanced
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
   exit;
}

define( 'GFPA_VERSION', '2.0.0' );
define( 'GFPA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GFPA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GFPA_PLUGIN_FILE', __FILE__ );
define( 'GFPA_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'GFPA_REVIEW_URL', 'https://wordpress.org/support/plugin/genesis-featured-page-advanced/reviews/?filter=5' );

register_activation_hook( __FILE__, 'fpa_activation_check' );
/**
 * This function runs on plugin activation. It checks to make sure the required
 * minimum Genesis version is installed. If not, it deactivates itself.
 *
 * 	Author: Nathan Rice
 *	Author URI: http://www.nathanrice.net/
 */
function fpa_activation_check() {
	$latest = '2.0';
	$theme_info = wp_get_theme( 'genesis' );

	if ( 'genesis' != basename( TEMPLATEPATH ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate plugin
		wp_die( sprintf( __( 'Sorry, you can\'t activate %1$sGenesis - Featured Page Advanced%2$s unless you have installed the %3$sGenesis Framework%4$s. Go back to the %5$sPlugins Page%4$s.', 'genesis-featured-page-advanced' ), '<em>', '</em>', '<a href="http://www.studiopress.com/themes/genesis" target="_blank">', '</a>', '<a href="javascript:history.back()">' ) );
	}

	if ( version_compare( $theme_info['Version'], $latest, '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate plugin
		wp_die( sprintf( __( 'Sorry, you can\'t activate %1$sGenesis - Featured Page Advanced%2$s unless you have installed the %3$sGenesis %4$s%5$s. Go back to the %6$sPlugins Page%5$s.', 'genesis-featured-page-advanced' ), '<em>', '</em>', '<a href="http://www.studiopress.com/themes/genesis" target="_blank">', $latest, '</a>', '<a href="javascript:history.back()">' ) );
	}
}


if ( ! class_exists( 'GenesisFeaturedPageAdvanced' ) ) :
	/**
	 * Main GenesisFeaturedPageAdvanced Class.
	 *
	 * @since 2.0.0
	 */
	final class GenesisFeaturedPageAdvanced {
		/**
		 * This plugin's instance.
		 *
		 * @var GenesisFeaturedPageAdvanced
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Main GenesisFeaturedPageAdvanced Instance.
		 *
		 * Insures that only one instance of GenesisFeaturedPageAdvanced exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @return object|GenesisFeaturedPageAdvanced The one true GenesisFeaturedPageAdvanced
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof GenesisFeaturedPageAdvanced ) ) {
				self::$instance = new GenesisFeaturedPageAdvanced();
				self::$instance->init();
				self::$instance->includes();
			}
			return self::$instance;
		}

		/**
		 * Throw error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'genesis-featured-page-advanced' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'genesis-featured-page-advanced' ), '1.0' );
		}

		/**
		 * Include required files.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		private function includes() {
			require_once GFPA_PLUGIN_DIR . 'includes/class-gfpa-block-assets.php';
            require_once GFPA_PLUGIN_DIR . 'includes/get-dynamic-blocks.php';
            
            require_once GFPA_PLUGIN_DIR . 'includes/widget-class.php';

			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
				require_once GFPA_PLUGIN_DIR . 'includes/admin/class-gfpa-action-links.php';
			}
		}

		/**
		 * Load actions
		 *
         * @since 2.0.0
		 * @return void
		 */
		private function init() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 99 );
			add_action( 'enqueue_block_editor_assets', array( $this, 'block_localization' ) );
            
            add_action( 'widgets_init', array( $this, 'register_widget' ) );
		}

		/**
         * @TODO Figure this out
		 * If debug is on, serve unminified source assets.
		 *
		 * @since 1.0.0
		 * @param string|string $type The type of resource.
		 * @param string|string $directory Any extra directories needed.
		 */
		/*public function asset_source( $type = 'js', $directory = null ) {

			if ( 'js' === $type ) {
				return GFPA_PLUGIN_URL . 'dist/' . $type . '/' . $directory;
			} else {
				return GFPA_PLUGIN_URL . 'dist/css/' . $directory;
			}
		}*/

		/**
		 * Loads the plugin language files.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'genesis-featured-page-advanced', false, basename( GFPA_PLUGIN_DIR ) . '/languages' );
		}

		/**
         * Enqueue localization data for our blocks.
		 *
		 * @since 2.0.0
         * @return void
		 */
		public function block_localization() {
			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'genesis-featured-page-advanced-editor-js', 'genesis-featured-page-advanced', GFPA_PLUGIN_DIR . '/languages' );
			}
		}
        
        /**
		 * Loads the widget
		 *
		 * @since 2.0.0
		 * @return void
		 */
        public function register_widget_class() {
        	register_widget( 'Genesis_Featured_Page_Advanced' );
        }
        
		/**
        * @TODO Figure this out
		 * Is an AMP endpoint.
		 *
		 * @return bool Whether the current response will be AMP.
		 */
		/*public function is_amp() {
			return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
		}*/
	}
endif;

/**
 * The main function for that returns GenesisFeaturedPageAdvanced
 *
 * @since 1.0.0
 * @return object|GenesisFeaturedPageAdvanced The one true GenesisFeaturedPageAdvanced Instance.
 */
function genesis_featured_page_advanced() {
	return GenesisFeaturedPageAdvanced::instance();
}

// Get the plugin running. Load on plugins_loaded action to avoid issue on multisite.
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	add_action( 'plugins_loaded', 'genesis_featured_page_advanced', 90 );
} else {
	genesis_featured_page_advanced();
}

