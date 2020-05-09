<?php
/**
 * Add links on the plugins admin page.
 *
 * @package GenesisFeaturedPageAdvanced
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generates a link.
 */
class GFPA_Action_Links {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Plugin row meta links
	 *
 	 * @since 1.0.0
	 *
	 * @param array  $plugin_meta 	An array of the plugin's metadata.
	 * @param string $plugin_file 	Path to the plugin file.
	 * @return array $plugin_meta	Updated plugin metadata
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file ) {

		// If we are not on the correct plugin, abort.
		if ( ! defined( 'GFPA_PLUGIN_BASE' ) ) {
			define( 'GFPA_PLUGIN_BASE', null );
		}

		if ( GFPA_PLUGIN_BASE === $plugin_file ) {
			$new_meta = array(
				'review' => '<a href="' . esc_url( GFPA_REVIEW_URL ) . '" aria-label="' . esc_attr( __( 'Review Genesis Featured Page Advanced on WordPress.org', 'GFPA_textdomain' ) ) . '" target="_blank">' . __( 'Leave a Review', 'GFPA_textdomain' ) . '</a>',
			);

			$plugin_meta = array_merge( $plugin_meta, $new_meta );
		}

		return $plugin_meta;
	}
}

new GFPA_Action_Links();
