/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';
import { SelectControl, Spinner } from '@wordpress/components';

class PageSelectControl extends Component {
	render() {
		const {
			className,
			label,
			hideLabelFromVision,
			help,
			value,
			onChange,
			allPages,
		} = this.props;

		if ( allPages ) {
			const pageId = value;
			const pageDefault = [ {
				value: '',
				label: __( 'Select a Page', 'genesis-featured-page-advanced' ),
			} ];

			const pages = allPages.map( ( page ) => ( {
				value: page.id,
				label: page.title,
			} ) );

			const pageOptions = pageDefault.concat( pages );

			return (
				<>
					<SelectControl
						className={ className }
						label={ label }
						hideLabelFromVision={ hideLabelFromVision }
						help={ help }
						value={ pageId }
						options={ pageOptions }
						onChange={ ( newValue ) => onChange( newValue ) }
					/>
				</>
			);
		}

		return (
			<div className="components-base-control page-selection-loading">
				{ ! hideLabelFromVision && (
					<label className="spinner-label">
						{ label }
					</label>
				) }
				<div className="spinner-container">
					<Spinner />
				</div>
			</div>
		);
	}
}

export default compose( [
	withSelect( ( select ) => {
		const { getEntityRecords } = select( 'core' );
		const { getCurrentPostId } = select( 'core/editor' );

		const currentPageId = getCurrentPostId() || '';

		const allPagesQuery = {
			per_page: -1,
			exclude: currentPageId, // We want to remove the current page so it is not selectable
			orderby: 'title',
			order: 'asc',
			status: 'publish',
		};

		let allPages = getEntityRecords( 'postType', 'page', allPagesQuery );

		if ( allPages ) {
			allPages = allPages.map( ( page ) => {
				return {
					id: page.id,
					title:
						page.title.rendered.trim() ||
						__( '(no title)', 'genesis-featured-page-advanced' ),
				};
			} );
		}

		return {
			allPages,
		};
	} ),
] )( PageSelectControl );
