/**
 * External dependencies
 */
import classnames from 'classnames';
import { get, map, filter } from 'lodash';

/**
 * Internal dependencies
 */
import icon from './icon';
import icons from './icons';
import Controls from './controls';
import InspectorControls from './inspector';
import TitleControl from './components/title-control';
import ImageControl from './components/image-control';
import PageSelectControl from './components/page-select-control';
import LinkPopover from './components/link-popover';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Component, RawHTML } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';
import { RichText, BlockIcon } from '@wordpress/block-editor';
import {
	TextControl,
	Placeholder,
	Button,
	Spinner,
} from '@wordpress/components';

/**
 * Block edit function
 */
class Edit extends Component {
	constructor() {
		super( ...arguments );

		this.onChangeInitialPageId = this.onChangeInitialPageId.bind( this );
		this.onSubmitPageId = this.onSubmitPageId.bind( this );
		this.onChangeInitialUrl = this.onChangeInitialUrl.bind( this );
		this.onSubmitCustomUrl = this.onSubmitCustomUrl.bind( this );
		this.onClickBack = this.onClickBack.bind( this );

		this.getImageSizeOptions = this.getImageSizeOptions.bind( this );

		this.state = {
			isInitialState: null,
			initialPageId: null,
			initialCustomUrl: null,
		};
	}

	componentWillMount() {
		const { pageType } = this.props.attributes;

		this.setState( { isInitialState: ! pageType } );
	}

	onChangeInitialPageId( initialPageId ) {
		this.setState( { initialPageId } );
	}

	onSubmitPageId() {
		const { setAttributes } = this.props;
		const { initialPageId } = this.state;

		setAttributes( { pageId: initialPageId } );
		this.setState( { isInitialState: false } );
	}

	onChangeInitialUrl( initialCustomUrl ) {
		this.setState( { initialCustomUrl } );
	}

	onSubmitCustomUrl() {
		const { setAttributes } = this.props;
		const { initialCustomUrl } = this.state;

		setAttributes( { CustomUrl: initialCustomUrl } );
		this.setState( { isInitialState: false } );
	}

	onClickBack() {
		const { setAttributes } = this.props;
		setAttributes( { pageType: null } );
	}

	getImageSizeOptions() {
		const { imageSizes, image } = this.props;

		return map(
			filter( imageSizes, ( { slug } ) =>
				get( image, [ 'media_details', 'sizes', slug, 'source_url' ] )
			),
			( { name, slug } ) => ( { value: slug, label: name } )
		);
	}

	render() {
		const {
			attributes,
			className,
			setAttributes,
			featuredPage,
		} = this.props;

		const {
			pageType,
			pageId,
			customUrl,
			enableImage,
			enableTitle,
			titleType,
			customTitle,
			enableTitleLink,
			positionTitleAbove,
			enableContent,
			contentType,
			contentWordCount,
			customContent,
			enableMoreLink,
			moreLinkText,
			moreLinkNewLine,
		} = attributes;

		// Render the block example in the block selector
		if ( pageType === 'example' ) {
			return (
				<article className="wp-block-gfpa-featured-page">
					<figure className="featured-image">
						<img
							src="http://blocks:8888/wp-content/uploads/2019/07/Deer_reduced.jpg"
							alt={ __(
								'Featured Image',
								'genesis-featured-page-advanced'
							) }
						/>
					</figure>
					<header className="entry-header">
						<h4 className="entry-title">
							{ __(
								'Featured Page Title',
								'genesis-featured-page-advanced'
							) }
						</h4>
					</header>
					<div className="entry-content">
						<p>
							Lorem ipsum dolor sit amet, consectetur adipiscing
							elit. In sed ligula urna. Donec tortor ligula,
							euismod vitae nibh sit amet, pulvinar ornare justo.
							Proin ullamcorper lacinia sapien, at convallis
							ante...
						</p>
					</div>
					<a href="http://example.com">
						{ __( 'Read More', 'genesis-featured-page-advanced' ) }
					</a>
				</article>
			);
		}

		const isInitialState = this.state.isInitialState;

		// Triggers when a user first adds the block to the page and no set pageType
		if ( isInitialState ) {
			let instructions = __(
				'Choose the content type you would like to feature. Pick a page on this website, or a custom URL.',
				'genesis-featured-page-advanced'
			);

			if ( pageType === 'page' ) {
				instructions = __(
					'Choose the page you would like to feature.',
					'genesis-featured-page-advanced'
				);
			} else if ( pageType === 'url' ) {
				instructions = __(
					'Enter the URL you would like to feature.',
					'genesis-featured-page-advanced'
				);
			}

			return (
				<div className={ classnames( className, 'initial-state' ) }>
					<Placeholder
						icon={ <BlockIcon icon={ icon } /> }
						label={ __(
							'Genesis - Featured Page',
							'genesis-featured-page-advanced'
						) }
						instructions={ instructions }
					>
						{ [
							! pageType && (
								<>
									<Button
										onClick={ () => setAttributes( { pageType: 'page' } ) }
										isPrimary
									>
										{ __(
											'Page',
											'genesis-featured-page-advanced'
										) }
									</Button>
									<Button
										onClick={ () => setAttributes( { pageType: 'url' } )  }
										isTertiary
									>
										{ __(
											'Custom URL',
											'genesis-featured-page-advanced'
										) }
									</Button>
								</>
							),
							pageType === 'page' && (
								<form onSubmit={ this.onSubmitPageId }>
									<div className="components-placeholder__fieldset">
										<PageSelectControl
											label={ __(
												'Select a Featured Page',
												'genesis-featured-page-advanced'
											) }
											hideLabelFromVision
											value={ this.state.initialPageId }
											onChange={ this.onChangeInitialPageId }
										/>
									</div>
									<div className="components-placeholder__button-group">
										<Button
											isPrimary
											disabled={ ! this.state.initialPageId }
											type="submit"
										>
											{ __(
												'Use Selected Page',
												'genesis-featured-page-advanced'
											) }
										</Button>
										<Button
											onClick={ this.onClickBack }
											isTertiary
										>
											/* translators: go back and choose a different page type */
											{ __(
												'Back',
												'genesis-featured-page-advanced'
											) }
										</Button>
									</div>
								</form>
							),
							pageType === 'url' && (
								<form onSubmit={ this.onSubmitCustomUrl }>
									<TextControl
										label={ __(
											'Enter a Custom URL',
											'genesis-featured-page-advanced'
										) }
										hideLabelFromVision
										help={ __(
											'This will direct all links in the block to the Custom URL. Include the full path, i.e. https://',
											'genesis-featured-page-advanced'
										) }
										type="url"
										value={ this.state.initialCustomUrl }
										onChange={ this.onChangeInitialCustomUrl}
										placeholder="http://www.example.com"
									/>
									<div className="components-placeholder__button-group">
										<Button
											isPrimary
											disabled={ ! this.state.initialCustomUrl }
											type="submit"
										>
											{ __(
												'Use URL',
												'genesis-featured-page-advanced'
											) }
										</Button>
										<Button
											onClick={ this.onClickBack }
											isTertiary
										>
											/* translators: go back and choose a different page type */
											{ __(
												'Back',
												'genesis-featured-page-advanced'
											) }
										</Button>
									</div>
								</form>
							),
						] }
					</Placeholder>
				</div>
			);
		}

		const imageSizeOptions = this.getImageSizeOptions();

		const getInspectorControls = (
			<InspectorControls
				imageSizeOptions={ imageSizeOptions }
				{ ...this.props }
			/>
		);

		// Triggers if user originally selected custom URL and then switched to
		// a page, no ID will have been selected yet.
		if ( ! isInitialState && pageType === 'page' && ! pageId ) {
			return (
				<>
					{ getInspectorControls }
					<div className={ classnames( className, 'no-page-id' ) }>
						<Placeholder
							icon={ <BlockIcon icon={ icons.errorOutline } /> }
							label={ __(
								'No Page Selected',
								'genesis-featured-page-advanced'
							) }
							instructions={ __(
								'Choose the page you would like to feature.',
								'genesis-featured-page-advanced'
							) }
						>
							<PageSelectControl
								label={ __(
									'Select a Featured Page',
									'genesis-featured-page-advanced'
								) }
								hideLabelFromVision
								value={ pageId }
								onChange={ ( value ) => setAttributes( { pageId: value } ) }
							/>
						</Placeholder>
					</div>
				</>
			);
		}

		// Triggers if there is a set page, but the page has not yet loaded
		if ( pageType === 'page' && featuredPage === undefined ) {
			return (
				<div className={ classnames( className, 'is-loading' ) } >
					<Placeholder
						icon={ <BlockIcon icon={ icon } /> }
						label={ __(
							'Loading Page...',
							'genesis-featured-page-advanced'
						) }
						instructions={ <Spinner /> }
					/>
				</div>
			);
		}

		let linkUrl = null;

		if ( pageType === 'page' ) {
			linkUrl = featuredPage.link.trim();
		} else if ( pageType === 'url' && customUrl ) {
			linkUrl = customUrl.trim();
		}

		const getBlockControls = (
			<Controls { ...this.props } />
		);

		const getImage = enableImage && (
			<ImageControl
				imageSizeOptions={ imageSizeOptions }
				linkUrl={ linkUrl }
				{ ...this.props }
			/>
		);

		const pageTitle = featuredPage && featuredPage.title.rendered
			? <RawHTML>{ featuredPage.title.rendered.trim() }</RawHTML>
			: __( '(no title)', 'genesis-featured-page-advanced' );

		const getTitle = enableTitle && (
			<header className="entry-header">
				{ [
					pageType === 'page' && titleType === 'page' && (
						<h4 className="entry-title">
							{ enableTitleLink
								? (
									<LinkPopover url={ linkUrl }>
										{ pageTitle }
									</LinkPopover>
								) : (
									pageTitle
								)
							}
						</h4>
					),
					<TitleControl
						enabled={
							pageType === 'url' || titleType === 'custom'
								? true
								: false
						}
						linked={ enableTitleLink }
						linkUrl={ linkUrl }
						customClass={ 'entry-title' }
						placeholder={ __(
							'Write the custom page title...',
							'genesis-featured-page-advanced'
						) }
						value={ customTitle }
						onChange={ ( value ) =>
							setAttributes( { customTitle: value } )
						}
						tagName="h4"
						allowedFormats={ [] }
						{ ...this.props }
					/>,
				] }
			</header>
		);

		let pageExcerpt;

		if ( featuredPage && featuredPage.excerpt.rendered ) {
			pageExcerpt = contentWordCount < featuredPage.excerpt.rendered.trim().split( ' ' ).length
				? featuredPage.excerpt.rendered.trim().split( ' ', contentWordCount ).join( ' ' ) + '... '
				: featuredPage.excerpt.rendered.trim();
		} else {
			pageExcerpt = __( '(no excerpt)', 'genesis-featured-page-advanced' );
		}

		const getContent = enableContent && (
			<div
				className={ classnames( 'entry-content', {
					[ `content-type-${ contentType }` ]: contentType,
					'has-more-link': enableMoreLink,
					'new-line': enableMoreLink && moreLinkNewLine,
				} ) }
			>
				{ pageType === 'page' && contentType === 'excerpt' && (
					<RawHTML>{ pageExcerpt }</RawHTML>
				) }
				{ ( pageType === 'url' || contentType === 'custom' ) && (
					<RichText
						placeholder={ __(
							'Write custom page content...',
							'genesis-featured-page-advanced'
						) }
						value={ customContent }
						onChange={ ( value ) =>
							setAttributes( { customContent: value } )
						}
						tagName="p"
					/>
				) }
				{ enableMoreLink && (
					<LinkPopover url={ linkUrl } className={ 'more-link' }>
						{ moreLinkText
							? moreLinkText
							: __( 'Read More', 'genesis-featured-page-advanced' )
						}
					</LinkPopover>
				) }
			</div>
		);

		return (
			<>
				{ getBlockControls }
				{ getInspectorControls }
				<article
					className={ classnames( className, {
						[ `type-${ pageType }` ]: pageType,
						[ `post-${ pageId }` ]: pageType === 'page' && pageId,
					} ) }
				>
					{ [
						positionTitleAbove && (
							<>
								{ getTitle }
								{ getImage }
							</>
						),
						! positionTitleAbove && (
							<>
								{ getImage }
								{ getTitle }
							</>
						),
					] }
					{ getContent }
				</article>
			</>
		);
	}
}

export default compose( [
	withSelect( ( select, props ) => {
		const { getEntityRecord, getMedia } = select( 'core' );
		const { getSettings } = select( 'core/block-editor' );
		const { imageSizes } = getSettings();
		const { attributes } = props;
		const { pageId, pageType, imageType, customImageId } = attributes;

		let featuredPage;

		if ( pageType === 'page' && pageId ) {
			featuredPage = getEntityRecord( 'postType', 'page', pageId );
		}

		let image = null;

		if ( pageType === 'page' && imageType === 'featured' && featuredPage ) {
			image = featuredPage.featured_media
				? getMedia( featuredPage.featured_media )
				: null;
		} else if ( customImageId ) {
			image = getMedia( customImageId );
		}

		return {
			imageSizes,
			featuredPage,
			image,
		};
	} ),
] )( Edit );
