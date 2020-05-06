/**
 * Internal dependencies
 */
import icons from './icons';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { Button, Toolbar } from '@wordpress/components';
import { BlockControls, MediaUpload } from '@wordpress/block-editor';

class Controls extends Component {
	constructor() {
		super( ...arguments );
		this.onSelectImage = this.onSelectImage.bind( this );
	}

	onSelectImage( image ) {
		const { setAttributes } = this.props;

		if ( ! image || ! image.url ) {
			setAttributes( { customImageId: undefined } );
			return;
		}
		setAttributes( { customImageId: image.id } );
	}

	render() {
		const { pageType, customImageId, imageType } = this.props.attributes;

		if (
			( pageType === 'url' || imageType === 'custom' ) &&
			customImageId
		) {
			return (
				<BlockControls>
					<Toolbar>
						<MediaUpload
							onSelect={ this.onSelectImage }
							type="image"
							value={ customImageId }
							render={ ( { open } ) => (
								<Button
									className="components-toolbar__control"
									label={ __(
										'Replace image',
										'genesis-featured-page-advanced'
									) }
									icon={ icons.replaceImage }
									onClick={ open }
								/>
							) }
						/>
					</Toolbar>
				</BlockControls>
			);
		}

		return null;
	}
}

export default Controls;
