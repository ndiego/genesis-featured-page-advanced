/**
 * Internal dependencies
 */
import icons from './../icons';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { withState } from '@wordpress/compose';
import { Popover, ExternalLink } from '@wordpress/components';
import { BlockIcon } from '@wordpress/block-editor';

function LinkPopover( {
	className,
	url,
	position,
	children,
	isVisible,
	setState,
} ) {
	const togglePopover = () => {
		setState( ( state ) => ( { isVisible: ! state.isVisible } ) );
	};
	const popoverPosition = position ? position : 'bottom center';

	let content = (
		<div className="missing-url">
			<BlockIcon icon={ icons.link } />
			{ __(
				'There is no Custom URL set.',
				'genesis-featured-page-advanced'
			) }
		</div>
	);
	if ( url ) {
		content = (
			<ExternalLink href={ url }>
				<span>{ url }</span>
			</ExternalLink>
		);
	}

	const linkClass = className
		? ( 'has-link-popover', className )
		: 'has-link-popover';

	return (
		<a className={ linkClass } onClick={ togglePopover } href="javascript:">
			{ children }
			{ isVisible && (
				<Popover
					focusOnMount="container"
					position={ popoverPosition }
					onFocusOutside={ togglePopover }
				>
					<div
						tabIndex="-1"
						className="wp-block-gfpa-featured-page_link-popover-container"
					>
						<p
							className="screen-reader-text"
							id="link-popover-container-label"
						>
							{ __(
								'Block is linked to:',
								'genesis-featured-page-advanced'
							) }
						</p>
						<div
							aria-labelledby="link-popover-container-label"
							aria-selected="true"
						>
							{ content }
						</div>
					</div>
				</Popover>
			) }
		</a>
	);
}

export default withState( {
	isVisible: false,
} )( LinkPopover );
