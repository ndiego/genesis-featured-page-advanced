/**
 * External dependencies
 */
import classnames from 'classnames';
import { find } from 'lodash';

/**
 * Internal dependencies
 */
import PageSelectControl from './components/page-select-control';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import {
	InspectorControls,
	//InspectorAdvancedControls,
	MediaUpload,
} from '@wordpress/block-editor';
import {
	PanelBody,
	//PanelRow,
	ToggleControl,
	SelectControl,
	RadioControl,
	RangeControl,
	TextControl,
	TextareaControl,
	ExternalLink,
	Button,
} from '@wordpress/components';

/**
 * Inspector controls
 */
class Inspector extends Component {
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
		const {
			attributes,
			setAttributes,
			image,
			imageSizeOptions,
		} = this.props;

		const {
			pageType,
			pageId,
			customUrl,
			enableImage,
			imageType,
			customImageId,
			imageAltText,
			imageSize,
			imageAlignment,
			enableImageLink,
			enableTitle,
			titleType,
			enableTitleLink,
			positionTitleAbove,
			enableContent,
			contentType,
			contentWordCount,
			enableMoreLink,
			moreLinkText,
			moreLinkNewLine,
			linkNewTab,
			linkNoFollow,
			linkSponsored,
		} = attributes;

		// If set image size does not exist for the current image, default to 'full'
		const imageSizeValue = find( imageSizeOptions, { value: imageSize } )
			? imageSize
			: 'full';
		const hasCustomImage =
			( imageType === 'custom' || pageType === 'url' ) && image
				? true
				: false;

		return (
			<InspectorControls>
				<PanelBody
					title={ __(
						'Page settings',
						'genesis-featured-page-advanced'
					) }
				>
					<RadioControl
						label={ __(
							'Page Type',
							'genesis-featured-page-advanced'
						) }
						help={ __(
							'Choose the type of page you would like to feature.',
							'genesis-featured-page-advanced'
						) }
						selected={ pageType }
						options={ [
							{
								label: __(
									'Page',
									'genesis-featured-page-advanced'
								),
								value: 'page',
							},
							{
								label: __(
									'Custom URL',
									'genesis-featured-page-advanced'
								),
								value: 'url',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { pageType: value } )
						}
					/>
					{ [
						pageType === 'page' && (
							<PageSelectControl
								label={ __(
									'Featured Page',
									'genesis-featured-page-advanced'
								) }
								value={ pageId }
								onChange={ ( value ) =>
									setAttributes( { pageId: value } )
								}
							/>
						),
						pageType === 'url' && (
							<TextControl
								label={ __(
									'Custom URL',
									'genesis-featured-page-advanced'
								) }
								help={ __(
									'This will direct all widget links (title, image, more text) to the custom URL. Include the full path, i.e. http://',
									'genesis-featured-page-advanced'
								) }
								type="url"
								value={ customUrl }
								onChange={ ( value ) =>
									setAttributes( { customUrl: value } )
								}
								placeholder="http://www.example.com"
							/>
						),
					] }
				</PanelBody>
				<PanelBody
					title={ __(
						'Image settings',
						'genesis-featured-page-advanced'
					) }
				>
					<ToggleControl
						label={ __(
							'Enable image',
							'genesis-featured-page-advanced'
						) }
						checked={ enableImage }
						onChange={ () =>
							setAttributes( { enableImage: ! enableImage } )
						}
					/>
					{ enableImage && (
						<>
							{ pageType === 'page' && (
								<RadioControl
									label={ __(
										'Image type',
										'genesis-featured-page-advanced'
									) }
									selected={ imageType }
									options={ [
										{
											label: __(
												'Featured Image',
												'genesis-featured-page-advanced'
											),
											value: 'featured',
										},
										{
											label: __(
												'Custom Image',
												'genesis-featured-page-advanced'
											),
											value: 'custom',
										},
									] }
									onChange={ ( value ) =>
										setAttributes( { imageType: value } )
									}
								/>
							) }
							{ hasCustomImage && (
								<div
									className={ classnames(
										'components-base-control',
										'replace-image'
									) }
								>
									<MediaUpload
										onSelect={ this.onSelectImage }
										type="image"
										value={ customImageId }
										render={ ( { open } ) => (
											<Button
												label={ __(
													'Replace image',
													'genesis-featured-page-advanced'
												) }
												isSecondary
												onClick={ open }
											>
												{ __(
													'Replace image',
													'genesis-featured-page-advanced'
												) }
											</Button>
										) }
									/>
								</div>
							) }
							<ToggleControl
								label={ __(
									'Enable image link',
									'genesis-featured-page-advanced'
								) }
								checked={ enableImageLink }
								onChange={ () =>
									setAttributes( {
										enableImageLink: ! enableImageLink,
									} )
								}
							/>
							<SelectControl
								label={ __(
									'Image size',
									'genesis-featured-page-advanced'
								) }
								value={ imageSizeValue }
								options={ imageSizeOptions }
								onChange={ ( value ) =>
									setAttributes( { imageSize: value } )
								}
							/>
							<SelectControl
								label={ __(
									'Image alignment',
									'genesis-featured-page-advanced'
								) }
								value={ imageAlignment }
								options={ [
									{
										label: __(
											'None',
											'genesis-featured-page-advanced'
										),
										value: 'none',
									},
									{
										label: __(
											'Left',
											'genesis-featured-page-advanced'
										),
										value: 'left',
									},
									{
										label: __(
											'Right',
											'genesis-featured-page-advanced'
										),
										value: 'right',
									},
									{
										label: __(
											'Center',
											'genesis-featured-page-advanced'
										),
										value: 'center',
									},
								] }
								onChange={ ( value ) =>
									setAttributes( { imageAlignment: value } )
								}
							/>
							<TextareaControl
								label={ __(
									'Alt text (alternative text)',
									'genesis-featured-page-advanced'
								) }
								value={ imageAltText }
								onChange={ ( value ) =>
									setAttributes( { imageAltText: value } )
								}
								help={
									<>
										<ExternalLink href="https://www.w3.org/WAI/tutorials/images/decision-tree">
											{ __(
												'Describe the purpose of the image',
												'genesis-featured-page-advanced'
											) }
										</ExternalLink>
										{ __(
											'If left empty, the title will be used.',
											'genesis-featured-page-advanced'
										) }
									</>
								}
							/>
						</>
					) }
				</PanelBody>
				<PanelBody
					title={ __(
						'Title settings',
						'genesis-featured-page-advanced'
					) }
				>
					<ToggleControl
						label={ __(
							'Enable title',
							'genesis-featured-page-advanced'
						) }
						checked={ enableTitle }
						onChange={ () =>
							setAttributes( { enableTitle: ! enableTitle } )
						}
					/>
					{ enableTitle && (
						<>
							{ pageType === 'page' && (
								<RadioControl
									label={ __(
										'Title type',
										'genesis-featured-page-advanced'
									) }
									selected={ titleType }
									options={ [
										{
											label: __(
												'Page Title',
												'genesis-featured-page-advanced'
											),
											value: 'page',
										},
										{
											label: __(
												'Custom Title',
												'genesis-featured-page-advanced'
											),
											value: 'custom',
										},
									] }
									onChange={ ( value ) =>
										setAttributes( { titleType: value } )
									}
								/>
							) }
							<ToggleControl
								label={ __(
									'Enable title link',
									'genesis-featured-page-advanced'
								) }
								checked={ enableTitleLink }
								onChange={ () =>
									setAttributes( {
										enableTitleLink: ! enableTitleLink,
									} )
								}
							/>
							<ToggleControl
								label={ __(
									'Position title above image',
									'genesis-featured-page-advanced'
								) }
								help={ __(
									'By default, the Title is placed below the featured image.',
									'genesis-featured-page-advanced'
								) }
								checked={ positionTitleAbove }
								onChange={ () =>
									setAttributes( {
										positionTitleAbove: ! positionTitleAbove,
									} )
								}
							/>
						</>
					) }
				</PanelBody>
				<PanelBody
					title={ __(
						'Content settings',
						'genesis-featured-page-advanced'
					) }
				>
					<ToggleControl
						className="parent-setting"
						label={ __(
							'Enable content',
							'genesis-featured-page-advanced'
						) }
						checked={ enableContent }
						onChange={ () =>
							setAttributes( { enableContent: ! enableContent } )
						}
					/>
					{ pageType === 'page' && enableContent && (
						<>
							<RadioControl
								label={ __(
									'Content type',
									'genesis-featured-page-advanced'
								) }
								selected={ contentType }
								options={ [
									{
										label: __(
											'Page Excerpt',
											'genesis-featured-page-advanced'
										),
										value: 'excerpt',
									},
									{
										label: __(
											'Custom Content',
											'genesis-featured-page-advanced'
										),
										value: 'custom',
									},
								] }
								onChange={ ( value ) =>
									setAttributes( { contentType: value } )
								}
							/>
							{ contentType === 'excerpt' && (
								<RangeControl
									label={ __(
										'Content word count',
										'genesis-featured-page-advanced'
									) }
									value={ contentWordCount }
									onChange={ ( value ) =>
										setAttributes( {
											contentWordCount: value,
										} )
									}
									min={ 2 }
									max={ 200 }
								/>
							) }
						</>
					) }
					<ToggleControl
						label={ __(
							'Enable More Link',
							'genesis-featured-page-advanced'
						) }
						checked={ enableMoreLink }
						onChange={ () =>
							setAttributes( {
								enableMoreLink: ! enableMoreLink,
							} )
						}
					/>
					{ enableMoreLink && (
						<>
							<TextControl
								label={ __(
									'More Link text',
									'genesis-featured-page-advanced'
								) }
								type="string"
								value={ moreLinkText }
								onChange={ ( value ) =>
									setAttributes( { moreLinkText: value } )
								}
								placeholder={ __(
									'Read More',
									'genesis-featured-page-advanced'
								) }
							/>
							<ToggleControl
								label={ __(
									'Insert on new line',
									'genesis-featured-page-advanced'
								) }
								help={ __(
									'Places the More Link on a new line.',
									'genesis-featured-page-advanced'
								) }
								checked={ moreLinkNewLine }
								onChange={ () =>
									setAttributes( {
										moreLinkNewLine: ! moreLinkNewLine,
									} )
								}
							/>
						</>
					) }
				</PanelBody>
				<PanelBody
					title={ __(
						'Link settings',
						'genesis-featured-page-advanced'
					) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __(
							'Open in New Tab',
							'genesis-featured-page-advanced'
						) }
						checked={ linkNewTab }
						onChange={ () =>
							setAttributes( { linkNewTab: ! linkNewTab } )
						}
					/>
					<ToggleControl
						label={ __(
							'No Follow',
							'genesis-featured-page-advanced'
						) }
						checked={ linkNoFollow }
						onChange={ () =>
							setAttributes( { linkNoFollow: ! linkNoFollow } )
						}
					/>
					<ToggleControl
						label={ __(
							'Sponsored',
							'genesis-featured-page-advanced'
						) }
						checked={ linkSponsored }
						onChange={ () =>
							setAttributes( { linkSponsored: ! linkSponsored } )
						}
					/>
				</PanelBody>
			</InspectorControls>
		);
	}
}

export default Inspector;
