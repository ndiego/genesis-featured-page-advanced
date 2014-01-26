<?php

/*
Plugin Name: Genesis Featured Page Advanced
Plugin URI: http://www.outermostdesign.com/
Description: Adds an enhanced version of the Genesis - Featured Page widget. The Genesis Framework 2.0+ is required.
Version: 1.2.1
Author: Outermost Design
Author URI: http://www.outermostdesign.com/
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


register_activation_hook( __FILE__, 'fpa_activation' );
/**
 * This function runs on plugin activation. It checks to make sure Genesis
 * or a Genesis child theme is active. If not, it deactivates itself.
 *
 * 	Author: Nathan Rice
 *	Author URI: http://www.nathanrice.net/
 */
function fpa_activation() {

	if ( ! defined( 'PARENT_THEME_VERSION' ) || ! version_compare( PARENT_THEME_VERSION, '2.0', '>=' ) )
		fpa_deactivate( '2.0', '3.6' );

}

/**
 * Deactivate Genesis Featured Page Advanced widget if the user is not using the Genesis Framework or WP 3.6+
 *
 * Author: Nathan Rice
 * Author URI: http://www.nathanrice.net/
 */
function fpa_deactivate( $genesis_version = '2.0', $wp_version = '3.6' ) {
	
	deactivate_plugins( plugin_basename( __FILE__ ) );
	wp_die( sprintf( 'Sorry, the minimum requirements for <em>Genesis - Featured Page Advanced</em> are WordPress %s and <a href="%s" target="_blank">Genesis %s</a>. You must upgrade and/or purchase Genesis to use this plugin. Go back to the <a href="javascript:history.back()">Plugins Page.</a>', $wp_version, 'http://www.studiopress.com', $genesis_version) );
	
}


include_once dirname( __FILE__ ) . '/inc/fpa-functions.php';


add_action( 'widgets_init', 'fpa_register_widget' );
/**
 * Registers our Genesis Featured Page Advanced widget 
 */
function fpa_register_widget() {

     register_widget( 'Genesis_Featured_Page_Advanced' );
    
};


