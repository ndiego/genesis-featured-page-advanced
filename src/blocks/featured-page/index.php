<?php
/**
 * Server-side rendering of the `featured-page` block.
 *
 * @package GenesisFeaturedPageAdvanced
 */

/**
 * Renders the block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the block content.
 */
function gfpa_render_featured_page_block( $attributes ) {
	//echo "The block is working";

	global $wp_query;

	if ( $attributes[ 'pageType' ] == 'page' ) {

		// if we are featuring a page, start the loop
		$wp_query = new WP_Query(
			array(
				'page_id' => $attributes[ 'pageId' ]
			)
		);

		if ( have_posts() ) {

			while ( have_posts() ) {

				the_post();

				$article_markup_open = genesis_markup(
					array(
						'open'    => '<article %s>',
						'context' => 'entry',
						'params'  => array(
							'is_widget' => true,
						),
						'echo'    => false
					)
				);

				$page_title = get_the_title() ? get_the_title() : __( '(no title)', 'featured-page-advanced' );
				$page_title = apply_filters( 'genesis_featured_page_title', $page_title, $instance, $args );

				$link_url = get_permalink();

				// Get the featured image of a page, is there is one
				$featured_image = genesis_get_image( array(
					'format'  => 'html',
					'size'    => $attributes['imageSize'],
					'context' => 'featured-page-widget',
					'attr'    => genesis_parse_attr( 'entry-image-widget', array ( 'alt' => $page_title ) ),
				) );

				if ( empty( $attributes['contentWordCount'] ) ) {
					$page_content = $attributes['moreLinkNewLine'] ?
						get_the_content() :
						get_the_content( genesis_a11y_more_link( $attributes['more_text'] ) );
				} else {
					$page_content = $attributes['moreLinkNewLine'] ?
						get_the_content_limit( (int) $attributes['contentWordCount'], '' ) :
						get_the_content_limit( (int) $attributes['contentWordCount'], genesis_a11y_more_link( esc_html( $attributes['moreLinkNewLine'] ) ) );
				}

				$page_excerpt = get_the_excerpt();
			}
		}

		// Restore original query.
		wp_reset_query();

	} else {

		// If we are displaying an image, set the has-post-thumnail class. Needed to maintain vistual consistency on some Studiopress themes
		$has_image 	         = $attributes['enableImage'] ? 'has-post-thumbnail' : '';
		$article_markup_open = '<article class="custom-link page type-page status-publish ' . $has_image . ' entry" itemscope="itemscope" itemtype="http://schema.org/CreativeWork">';
		$link_url 	         = esc_url( $attributes['customUrl'] );
		$page_excerpt ='';
		$page_content ='';
	}

	$block_content = $article_markup_open;
	$block_content .= $attributes[ 'positionTitleAbove' ] ? fpa_get_title_heading( $attributes, $page_title, $link_url, $featured_image ) : '';
	$block_content .= fpa_get_image( $attributes, $featured_image, $link_url );
	$block_content .= ! $attributes[ 'positionTitleAbove' ] ? fpa_get_title_heading( $attributes, $page_title, $link_url, $featured_image ) : '';
	$block_content .= fpa_get_content( $attributes, $link_url, $page_content, $page_excerpt );
	$block_content .= '</article>';

	return $block_content;
}

function fpa_get_image( $attributes, $featured_image, $link_url ) {

	// Display the image if there is one
	if ( $attributes['enableImage'] ) {

		// Define the image alignment
		switch ( $attributes['imageAlignment'] ) {
			case 'right':
				$image_alignment = 'alignright';
				break;
			case 'center':
				$image_alignment = 'aligncenter';
				break;
			case 'left':
				$image_alignment = 'alignleft';
				break;
			default:
				$image_alignment = '';
				break;
		}

		// Define the role, if there is no page title the image takes on the role of title
		$role = ( ! $attributes['enableTitle'] ) ? '' : 'aria-hidden="true"';

		if ( $attributes['pageType'] == 'page' && $attributes['imageType'] == 'featured' && $featured_image ) {

			// Display the featured image
			$image       = $featured_image;
			$image_title = $page_title;

		} else if ( $attributes['imageType'] == 'custom' ) {

			// Display the custom image
			$atts        = $attributes['imageAlignment'] == 'center' ? array( 'class' => 'entry-image', 'style' => 'display:block;margin:0 auto;' ) : array( 'class' => 'entry-image' );
			$image       = wp_get_attachment_image( $attributes['customImageId'], $attributes['imageSize'], false, $atts );
			$image_title = $attributes['pageType'] == 'page' ? $page_title : esc_attr( $attributes['customTitle'] );

		}

		if ( $attributes['enableImageLink'] ) {

			$entry_image = sprintf(
				'<a href="%s" title="%s" class="%s" %s %s>%s</a>',
				$link_url,
				$image_title,
				$image_alignment,
				fpa_get_link_target_rel( $attributes ),
				$role,
				$image
			);
		} else {

			// The <span> replaces the <a> so the image alignment feature still works (unfortunately need to use text-align here, which is not optimal)
			$align_center_fix = $image_alignment == 'aligncenter' ? 'style="text-align:center"' : '';

			$entry_image = sprintf(
				'<span class="%s" %s %s>%s</span>',
				$image_alignment,
				$align_center_fix,
				$role,
				$image
			);
		}

		return $entry_image;
	}
}

function fpa_get_title_heading( $attributes, $page_title, $link_url ) {

	// Create the page title if there is one...
	if ( $attributes['enableTitle'] ) {

		$heading = genesis_a11y( 'headings' ) ? 'h4' : 'h2';
		$heading = apply_filters( 'fpa_page_title_heading', $heading );

		if ( $attributes['pageType'] == 'page' && $attributes['titleType'] == 'page' ) {

			$title_text = $page_title;

		} else if ( ( $attributes['pageType'] == 'page' && $attributes['titleType'] == 'custom' ) || $attributes['pageType'] == 'url' ) {

			$custom_title = ! empty( $attributes['customTitle'] ) ? esc_attr( $attributes['customTitle'] ) : __( '(no custom title)', 'featured-page-advanced' );
			$custom_title = apply_filters( 'genesis_featured_page_title', $custom_title, $instance, $args );

			$title_text = $custom_title;
		}

		if ( $attributes['enableTitleLink'] ) {

			$title = sprintf(
				'<a href="%s" title="%s" %s>%s</a>',
				$link_url,
				$title_text,
				fpa_get_link_target_rel( $attributes ),
				$title_text
			);
		} else {
			$title = $title_text;
		}

		$entry_title = genesis_markup(
			array(
				'open'    => "<{$heading} %s>",
				'close'   => "</{$heading}>",
				'context' => 'entry-title',
				'content' => $title,
				'params'  => array(
					'is_widget' => true,
					'wrap'      => $heading,
				),
				'echo'    => false
			)
		);

		$entry_header = genesis_markup(
			array(
				'open'    => '<header %s>',
				'close'   => '</header>',
				'context' => 'entry-header',
				'content' => $entry_title,
				'params'  => array(
					'is_widget' => true,
				),
				'echo'    => false
			)
		);

		return $entry_header;
	}
}




function fpa_get_content( $attributes, $link_url, $page_content, $page_excerpt ) {

	if ( $attributes['enableContent'] ) {

		// Set the More Link if the text exits
		if ( $attributes['enableMoreLink'] ) {

			$more_link = sprintf(
				'<a href="%s" class="more-link" %s>%s</a>',
				$link_url,
				fpa_get_link_target_rel( $attributes ),
				genesis_a11y_more_link( esc_attr( $attributes['moreLinkText'] ) )
			);
		} else {
			$more_link = '';
		}

		$entry_content = genesis_markup(
			array(
				'open'    => '<div %s>',
				'context' => 'entry-content',
				'params'  => array(
					'is_widget' => true,
				),
				'echo'	  => false
			)
		);

		if ( $attributes['pageType'] == 'page' ) {

			// Display page content
			if ( $attributes['contentType'] == 'content' ) {
				$entry_content .= $page_content;
			}

			// Display page excerpt
			if ( $attributes['contentType'] == 'excerpt' ) {
				if ( $attributes['enableMoreLink'] ) {
					$entry_content .= sprintf(
						'<p>%s</p>',
						$page_excerpt
					);
				} else {
					$entry_content .= sprintf(
						'<p>%s%s</p>',
						$page_excerpt,
						$more_link
					);
				}
			}

		}

		// Display custom content
		if ( $attributes['contentType'] == 'custom' && ! empty( $attributes['customContent'] ) ) {
			if ( $attributes['enableMoreLink'] ) {
				$entry_content .= sprintf(
					'<p>%s</p>',
					do_shortcode( wp_kses_post( $attributes['customContent'] ) )
				);
			} else {
				$entry_content .= sprintf(
					'<p>%s%s</p>',
					do_shortcode( wp_kses_post( $attributes['customContent'] ) ),
					$more_link
				);
			}
		}

		// Render the More Link on a new line
		if ( $attributes['enableMoreLink'] && $more_link ) {
			$entry_content .= sprintf(
				'<div class=%s>%s</div>',
				'fpa-more-link',
				$more_link
			);
		}

		$entry_content .= genesis_markup(
			array(
				'close'   => '</div>',
				'context' => 'entry-content',
				'params'  => array(
					'is_widget' => true,
				),
				'echo'	  => false
			)
		);

		return $entry_content;
	}
}





function fpa_get_link_target_rel( $attributes ) {

	$link_target = $attributes[ 'linkNewTab' ] ? 'target="_blank"' : '';
	$link_rel = '';

	if ( $attributes[ 'linkNoFollow' ] || $attributes[ 'linkSponsored' ] ) {
		$link_rel_nofollow = $attributes[ 'linkNoFollow' ] ? 'nofollow' : '';
		$link_rel_sponored = $attributes[ 'linkSponsored' ] ? 'sponsored' : '';

		$link_rel = 'rel="' . $link_rel_nofollow . ' ' . $link_rel_sponored . '"';
	}

	return $link_target . ' ' . $link_rel;
}

/**
 * Registers the block on server.
 */
function gfpa_register_featured_page_block() {
	// Return early if this function does not exist.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$slug = 'gfpa';
	$block = 'featured-page';

	// Load attributes from block.json.
	ob_start();
	include GFPA_PLUGIN_DIR . 'src/blocks/' . $block . '/block.json';
	$metadata = json_decode( ob_get_clean(), true );

	register_block_type(
		$slug . '/' . $block,
		array(
			'attributes'      	=> $metadata['attributes'],
			'render_callback' 	=> 'gfpa_render_featured_page_block',
			'editor_script' 	=> $slug . '-editor',
			'editor_style'  	=> $slug . '-editor',
			'style'         	=> $slug . '-frontend',
		)
	);
}
add_action( 'init', 'gfpa_register_featured_page_block' );

add_filter( 'image_size_names_choose', 'my_custom_sizes' );

function my_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'sidebar-featured' => __( 'Sidebar Featured' ),
    ) );
}
