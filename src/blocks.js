/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

// Register all blocks
import * as featuredPageBlock from './blocks/featured-page';

/**
 * Function to register an individual block.
 *
 * @param {Object} block The block to be registered.
 *
 */
const registerBlock = ( block ) => {
	if ( ! block ) {
		return;
	}

	const { name, category, settings } = block;

	registerBlockType( name, {
		category,
		...settings,
	} );
};

/**
 * Function to register blocks provided by the plugin.
 */
export const registerGFPABlocks = () => {
	[ featuredPageBlock ].forEach( registerBlock );
};

registerGFPABlocks();
