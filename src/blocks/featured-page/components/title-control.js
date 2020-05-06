/**
 * Internal dependencies
 */
import LinkPopover from './link-popover';
import icons from './../icons';

/**
 * WordPress dependencies
 */
import { RichText, BlockIcon } from '@wordpress/block-editor';

function TitleControl( {
	enabled,
	linked,
	linkUrl,
	customClass,
	placeholder,
	value,
	onChange,
	tagName,
	allowedFormats,
	...props
} ) {
	const title = value;
	const titleDisplay = RichText.isEmpty( title ) ? placeholder : title;
	const TitleTag = tagName;

	if ( enabled ) {
		return (
			<>
				{ props.isSelected && (
					<>
						<RichText
							className={ customClass }
							placeholder={ placeholder }
							value={ title }
							onChange={ ( newValue ) => onChange( newValue ) }
							tagName={ tagName }
							allowedFormats={ allowedFormats }
						/>
						{ linked && (
							<LinkPopover
								className="custom-title-link"
								url={ linkUrl }
							>
								<BlockIcon icon={ icons.link } />
							</LinkPopover>
						) }
					</>
				) }
				{ ! props.isSelected && (
					<TitleTag
						className={
							( RichText.isEmpty( title ) && 'no-title',
							'entry-title' )
						}
					>
						{ [
							linked && (
								<LinkPopover url={ linkUrl }>
									{ titleDisplay }
								</LinkPopover>
							),
							! linked && titleDisplay,
						] }
					</TitleTag>
				) }
			</>
		);
	}
}

export default TitleControl;
