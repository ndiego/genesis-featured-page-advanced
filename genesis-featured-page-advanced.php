<?php

/*
Plugin Name: Genesis Featured Page Advanced
Plugin URI: http://www.outermostdesign.com/
Description: Adds an enhanced version of the Genesis - Featured Page widget. The Genesis Framework 2.0+ is required.
Version: 1.3
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

		$theme_info = wp_get_theme();

		if ( 'genesis' != basename( TEMPLATEPATH ) ) {
	        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate plugin
			wp_die( sprintf( 'Sorry, you can\'t activate <em>Genesis - Featured Page Advanced</em> unless you have installed the <a href="%s" target="_blank">Genesis Framework</a>. Go back to the <a href="javascript:history.back()">Plugins Page</a>.', 'http://www.studiopress.com/themes/genesis' ) );
		}

		if ( version_compare( $theme_info['Version'], $latest, '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate plugin
			wp_die( sprintf( 'Sorry, you cannot activate <em>Genesis - Featured Page Advanced</em> without <a href="%s" target="_blank">Genesis %s</a> or greater. Go back to the <a href="javascript:history.back()">Plugins Page</a>.', 'http://www.studiopress.com/themes/genesis', $latest ) );
		}

}


include_once dirname( __FILE__ ) . '/inc/fpa-functions.php';


add_action( 'widgets_init', 'fpa_register_widget' );
/**
 * Registers our Genesis Featured Page Advanced widget 
 */
function fpa_register_widget() {

     register_widget( 'Genesis_Featured_Page_Advanced' );
    
};


