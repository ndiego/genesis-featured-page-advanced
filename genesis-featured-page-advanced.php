<?php
/*
Plugin Name: Genesis Featured Page Advanced
Plugin URI: http://www.outermost.co/
Description: Adds an enhanced version of the Genesis - Featured Page widget. The Genesis Framework 2.0+ is required.
Version: 1.9.7
Author: Nick Diego
Author URI: http://www.outermost.co/
Text Domain: genesis-featured-page-advanced
License: GPLv2

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'WPINC' ) or die;


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


add_action('plugins_loaded', 'fpa_load_textdomain');
/**
 * Load the plugin translation files
 */
function fpa_load_textdomain() {
	load_plugin_textdomain( 'genesis-featured-page-advanced', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}


/**
 * Include the Widget Class file
 */
include_once dirname( __FILE__ ) . '/inc/widget-class.php';


add_action( 'widgets_init', 'fpa_register_widget' );
/**
 * Registers our Genesis Featured Page Advanced widget
 */
function fpa_register_widget() {
	register_widget( 'Genesis_Featured_Page_Advanced' );
}
