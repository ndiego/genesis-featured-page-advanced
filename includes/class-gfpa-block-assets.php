<?php
/**
 * Load assets for our blocks.
 *
 * @package GenesisFeaturedPageAdvanced
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load general assets for our blocks.
 *
 * @since 1.0.0
 */
class GFPA_Block_Assets {


	/**
	 * This class's instance.
	 *
	 * @var GFPA_Block_Assets
	 */
	private static $instance;

	/**
	 * Registers the class.
	 */
	public static function register() {
		if ( null === self::$instance ) {
			self::$instance = new GFPA_Block_Assets();
		}
	}

	/**
     * The Plugin slug.
     *
     * @var string $slug
     */
    private $slug;

	/**
	 * The Constructor.
	 */
	public function __construct() {
        $this->slug = 'gfpa';

		add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
		add_action( 'init', array( $this, 'editor_assets' ) );

		// @// TODO: Not currently using these...
		//add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		//add_action( 'the_post', array( $this, 'frontend_scripts' ) );
	}

	/**
	 * Loads the asset file for the given script or style.
	 * Returns a default if the asset file is not found.
	 *
	 * @param string $filepath The name of the file without the extension.
	 *
	 * @return array The asset file contents.
	 */
	public function get_asset_file( $filepath ) {
		$asset_path = GFPA_PLUGIN_DIR . $filepath . '.asset.php';

		return file_exists( $asset_path )
			? include $asset_path
			: array(
				'dependencies' => array(),
				'version'      => GFPA_VERSION,
			);
	}

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function block_assets() {
        // Shortcut for the slug.
        $slug = $this->slug;

		// Styles.
		$filepath   = 'dist/' . $slug . '-style';
		$asset_file = $this->get_asset_file( $filepath );

		wp_enqueue_style(
			$slug . '-frontend',
			GFPA_PLUGIN_URL . $filepath . '.css',
			array(),
			$asset_file['version']
		);
	}

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function editor_assets() {
        // Shortcut for the slug.
        $slug = $this->slug;

		// Styles.
		$filepath   = 'dist/' . $slug . '-editor';
		$asset_file = $this->get_asset_file( $filepath );

		wp_register_style(
			$slug . '-editor',
			GFPA_PLUGIN_URL . $filepath . '.css',
			array(),
			$asset_file['version']
		);

		// Scripts.
		$filepath   = 'dist/' . $slug . '-blocks';
		$asset_file = $this->get_asset_file( $filepath );

		wp_register_script(
			$slug . '-editor',
			GFPA_PLUGIN_URL . $filepath . '.js',
			array_merge( $asset_file['dependencies'], array( 'wp-api' ) ),
			$asset_file['version'],
			true
		);

		@// TODO: We may not need this....
		wp_localize_script(
			$slug . '-blocks-scripts',
			$slug . 'BlockData',
			array(
				'testData' => array(
					'testOne' => 'Hello',
					'testTwo' => 'there',
				),
			)
		);
	}

	/**
	* @TODO Figure this out
	 * Enqueue front-end assets for blocks.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	/*public function frontend_scripts() {

		// Custom scripts are not allowed in AMP, so short-circuit.
		if ( SamplePlugin()->is_amp() ) {
			return;
		}

        // Refer to coblocks for more details on this if frontend scripts are needed, otherwise remove this section

	}*/

}

GFPA_Block_Assets::register();
