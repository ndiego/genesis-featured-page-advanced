/**
 * Internal dependencies
 */
import metadata from './block.json';
import icon from './icon';
import edit from './edit';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Block constants
 */
const { name, category } = metadata;

const settings = {
	/* translators: block name */
	title: __( 'Genesis - Featured Page', 'genesis-featured-page-advanced' ),
	/* translators: block description */
	description: __(
		'Displays a featured page with an image and content. Designed to mimic the Genesis - Featured Page Advanced widget.',
		'genesis-featured-page-advanced'
	),
	icon,
	keywords: [
		/* translators: block keyword */
		__( 'page', 'genesis-featured-page-advanced' ),
		/* translators: block keyword */
		__( 'genesis', 'genesis-featured-page-advanced' ),
		/* translators: block keyword */
		__( 'content', 'genesis-featured-page-advanced' ),
	],
	supports: {
		align: [ 'wide', 'full' ],
		html: false,
	},
	example: {
		attributes: { pageType: 'example' },
	},
	edit,
	save() {
		return null;
	},
};

export { name, category, settings };
