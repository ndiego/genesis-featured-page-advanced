<?php
/**
 * Run on plugin install.
 *
 * @package GenesisFeaturedPageAdvanced
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CoBlocks_Install Class
 */
class GFPA_Install {

	/**
	 * Constructor
	 */
	public function __construct() {
		register_activation_hook( GFPA_PLUGIN_FILE, array( $this, 'genesis_activation_check' ) );
	}

	/**
	 * This function runs on plugin activation. It checks to make sure the required
	 * minimum Genesis version is installed. If not, it deactivates itself.
	 *
	 * 	Author: Nathan Rice
	 *	Author URI: http://www.nathanrice.net/
	 */
	public function genesis_activation_check() {
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
}

return new GFPA_Install();