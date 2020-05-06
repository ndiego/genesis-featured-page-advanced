/**
 * External dependencies
 */
import classnames from 'classnames';
import { get, find, last } from 'lodash';

/**
 * Internal dependencies
 */
import icons from './../icons';
import LinkPopover from './link-popover';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { getPath } from '@wordpress/url';
import { MediaPlaceholder, BlockIcon } from '@wordpress/block-editor';
import { Placeholder, Spinner } from '@wordpress/components';

class ImageControl extends Component {
	constructor() {
		super( ...arguments );
		this.onSelectImage = this.onSelectImage.bind( this );
		this.getFilename = this.getFilename.bind( this );
		this.renderPlaceholder = this.renderPlaceholder.bind( this );
	}

	onSelectImage( image ) {
		const { setAttributes } = this.props;

		if ( ! image || ! image.url ) {
			setAttributes( { customImageId: undefined } );
			return;
		}
		setAttributes( { customImageId: image.id } );
	}

	getFilename( url ) {
		const path = getPath( url );
		if ( path ) {
			return last( path.split( '/' ) );
		}
	}

	renderPlaceholder() {
		const { attributes, image } = this.props;
		const { pageType, imageType } = attributes;

		const icon =
			!! image || image === undefined
				? icons.image
				: icons.errorOutline;

		const label =
			imageType === 'featured' && pageType !== 'url'
				? __( 'Featured Image', 'genesis-featured-page-advanced' )
				: __( 'Custom Image', 'genesis-featured-page-advanced' );

		const instructions =
			image === undefined
				? <Spinner />
				: __(
					'The selected page is missing a Featured Image. If you would like an image to display, either set a Featured Image on the page itself, or choose the Custom Image option.',
					'genesis-featured-page-advanced'
				);

		if (
			image === undefined ||
			( pageType === 'page' && imageType === 'featured' )
		) {
			return (
				<Placeholder
					icon={ <BlockIcon icon={ icon } /> }
					label={ label }
					instructions={ instructions }
				/>
			);
		}

		return (
			<MediaPlaceholder
				icon={ <BlockIcon icon={ icons.image } /> }
				labels={ {
					title: __(
						'Custom Image',
						'genesis-featured-page-advanced'
					),
					instructions: __(
						'Upload an image file or pick one from your media library.',
						'genesis-featured-page-advanced'
					),
				} }
				onSelect={ this.onSelectImage }
				allowedTypes={ 'image' }
				accept="image/*"
			/>
		);
	}

	render() {
		const { attributes, image, imageSizeOptions, linkUrl } = this.props;

		const {
			imageAlignment,
			enableImageLink,
			imageSize,
			imageAltText,
		} = attributes;

		if ( ! image ) {
			return this.renderPlaceholder();
		}

		const imageSizeValue = find( imageSizeOptions, { value: imageSize } )
			? imageSize
			: 'full';

		const imageUrl = get( image, [
			'media_details',
			'sizes',
			imageSizeValue,
			'source_url',
		] );

		const alignment =
			!! imageAlignment && imageAlignment !== 'none'
				? 'align' + imageAlignment
				: '';

		const filename = this.getFilename( imageUrl );
		let altText;
		if ( imageAltText ) {
			altText = imageAltText;
		} else if ( filename ) {
			altText = sprintf(
				/* translators: %s: file name */
				__(
					'This image has an empty alt attribute; its file name is %s',
					'genesis-featured-page-advanced'
				),
				filename
			);
		} else {
			altText = __(
				'This image has an empty alt attribute',
				'genesis-featured-page-advanced'
			);
		}

		const featuredImage = <img src={ imageUrl } alt={ altText } />;

		return (
			<figure className={ classnames( 'featured-image', alignment ) }>
				{ [
					enableImageLink && (
						<LinkPopover url={ linkUrl } position="middle center">
							{ featuredImage }
						</LinkPopover>
					),
					! enableImageLink && featuredImage,
				] }
			</figure>
		);
	}
}

export default ImageControl;
