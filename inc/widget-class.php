<?php

defined( 'WPINC' ) or die;

/**
 * Advanced Featured Page Widget class
 *
 * Original Author: Studiopress
 * Modifications & Enchancements by: Nick Diego (Outermost Design)
 *
 */
class Genesis_Featured_Page_Advanced extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 */
	public function __construct() {

		// Setup widget default options
		$this->defaults = array(
			'title'           			=> '',
			'widget_title_below'		=> 0,
			'enable_title_link'			=> 0,
			'feature_type'				=> 'page',
			'page_id'         			=> '',
			'custom_link'				=> '',
			'target_attr'				=> '_self',
			'rel_attr'					=> '',
			'show_image'				=> 1,
			'image_size'      			=> 'thumbnail',
			'image_alignment' 			=> 'alignnone',
			'enable_image_link'			=> 0,
			'custom_image'				=> '',
			'attachment_id'				=> 0,
			'custom_image_size'      	=> 'full',
			'custom_title'				=> '',
			'show_title'      			=> 0,
			'page_title_above'			=> 0,
			'enable_page_title_link'	=> 0,
			'show_content'    			=> 0,
			'show_excerpt'				=> 0,
			'show_custom_content'   	=> 0,
			'custom_content'  			=> '',
			'content_limit'   			=> '',
			'more_text'       			=> '',
			'more_text_new_line'		=> 0,
		);

		$widget_ops = array(
			'classname'   => 'featured-content featuredpage',
			'description' => __( 'Displays featured page an image and content.' , 'genesis-featured-page-advanced' ),
		);

		$control_ops = array(
			'id_base' => 'featured-page-advanced',
			'width'   => 200,
			'height'  => 250,
		);

		parent::__construct( 'featured-page-advanced', __( 'Genesis - Featured Page Advanced', 'genesis-featured-page-advanced' ), $widget_ops, $control_ops );

		// Enqueue Admin scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'fpa_admin_scripts_enqueue' ) );
	}


	/**
	 * Echo the widget content on the frontend.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	public function widget( $args, $instance ) {

		global $wp_query;

		// Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $args['before_widget'];

		// Get the widget title output if there is any...
		if ( ! empty( $instance['title'] ) ) {

			// Get the widget title text
			$widget_title_text = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

			// If a link is enabled, wrap the title text
			if ( $instance['enable_title_link'] ) {
				
				$widget_title_link_url = $instance['feature_type'] == 'page' ? 
					get_permalink( $instance['page_id'] ) :
					esc_url( $instance['custom_link'] );
					
				$widget_title_link_title = $instance['feature_type'] == 'page' ?
					get_the_title( $instance['page_id'] ) :
					esc_attr( $instance['title'] );
					
				$widget_title = sprintf( 
					'<a href="%s" title="%s" target="%s" rel="%s">%s</a>', 
					$widget_title_link_url, 
					$widget_title_link_title, 
					esc_attr( $instance['target_attr'] ), 
					esc_attr( $instance['rel_attr'] ), 
					$widget_title_text
				);
				
			} else {
				$widget_title = $widget_title_text;
			}

			// The complete widget title
			$widget_title = $args['before_title'] . $widget_title . $args['after_title'];
		} else {
			$widget_title = '';
		}

		// Display widget title above image
		if ( ! $instance['widget_title_below'] ) {
			echo $widget_title;
		}

		// If we are featuring a page, then use a loop and the built-in genesis_markup function, otherwise manually add markup
		if ( $instance['feature_type'] == 'page' ) {
			
			// if we are featuring a page, start the loop
			$wp_query = new WP_Query( 
				array( 
					'page_id' => $instance['page_id']
				)
			);

			if ( have_posts() ) {
				
				while ( have_posts() ) {
					
					the_post();

					$article_markup_open = genesis_markup( 
						array(
							'open'   => '<article %s>',
							'context' => 'entry',
							'params' => array(
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
						'size'    => $instance['image_size'],
						'context' => 'featured-page-widget',
						'attr'    => genesis_parse_attr( 'entry-image-widget', array ( 'alt' => $page_title ) ),
					) );
					
					if ( empty( $instance['content_limit'] ) ) {
						$page_content = $instance['more_text_new_line'] ? 
							get_the_content() : 
							get_the_content( genesis_a11y_more_link( $instance['more_text'] ) );
					} else {
						$page_content = $instance['more_text_new_line'] ? 
							get_the_content_limit( (int) $instance['content_limit'], '' ) : 
							get_the_content_limit( (int) $instance['content_limit'], genesis_a11y_more_link( esc_html( $instance['more_text'] ) ) );
					}
					
					$page_excerpt = get_the_excerpt();
				}
			}
			
			// Restore original query.
			wp_reset_query();
			
		} else {

			// If we are displaying an image, set the has-post-thumnail class. Needed to maintain vistual consistency on some Studiopress themes
			$has_image = ( $instance['show_image'] == 2 || $instance['show_image'] == 3 ) ? 'has-post-thumbnail' : '';
			
			$article_markup_open = '<article class="custom-link page type-page status-publish ' . $has_image . ' entry" itemscope="itemscope" itemtype="http://schema.org/CreativeWork">';
			
			$link_url = esc_url( $instance['custom_link'] );	
		}
		
		// Opening tag of the article		
		echo $article_markup_open;

		// Create the page title if there is one...
		if ( ! empty( $instance['show_title'] ) && $instance['show_title'] ) {

			$heading = genesis_a11y( 'headings' ) ? 'h4' : 'h2';
			$heading = apply_filters( 'fpa_page_title_heading', $heading );
			
			if ( $instance['feature_type'] == 'page' && $instance['show_title'] == 1 ) {
				
				$title_text = $page_title;
			
			} else if ( ( $instance['feature_type'] == 'page' && $instance['show_title'] == 2 ) || $instance['feature_type'] == 'custom' ) {
			
				$custom_title = ! empty( $instance['custom_title'] ) ? esc_attr( $instance['custom_title'] ) : __( '(no custom title)', 'featured-page-advanced' );
				$custom_title = apply_filters( 'genesis_featured_page_title', $custom_title, $instance, $args );
				
				$title_text = $custom_title;
			}
			
			if ( $instance['enable_page_title_link'] ) {
				$title = sprintf( 
					'<a href="%s" title="%s" target="%s" rel="%s">%s</a>', 
					$link_url, 
					$title_text, 
					esc_attr( $instance['target_attr'] ), 
					esc_attr( $instance['rel_attr'] ), 
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
					'echo'    => false,
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
					'echo'    => false,
				)
			);

		} else {
			$entry_header = '';
		}

		// Display entry header (page title) above image
		if ( $instance['page_title_above'] ) {
			echo $entry_header;
		}

		// Define the role, if there is no page title the image takes on the role of title
		$role = ( ! $instance['show_title'] ) ? '' : 'aria-hidden="true"';

		// Display featured image (Hide if using Custom Link)
		if ( $instance['feature_type'] == 'page' && $instance['show_image'] == 2 && $featured_image ) {

			if ( $instance['enable_image_link'] == 1 ) {
				printf( 
					'<a href="%s" title="%s" class="%s" target="%s" rel="%s" %s>%s</a>', 
					$link_url, 
					$page_title, 
					esc_attr( $instance['image_alignment'] ), 
					esc_attr( $instance['target_attr'] ), 
					esc_attr( $instance['rel_attr'] ), 
					$role, 
					$featured_image 
				);
			} else {
				// The <span> replaces the <a> so the image alignment feature still works (unfortunately need to use text-align here, which is not optimal)
				$align_center_fix = $instance['image_alignment'] == 'aligncenter' ? 'style="text-align:center"' : '';
				
				printf( '<span class="%s" %s %s>%s</span>', 
					esc_attr( $instance['image_alignment'] ), 
					$align_center_fix,
					$role, 
					$featured_image 
				);
			}
		}

		// Display custom image
		if ( $instance['show_image'] == 3 ) {

			$atts  = $instance['image_alignment'] == 'aligncenter' ? array( 'class' => 'entry-image', 'style' => 'display:block;margin:0 auto;' ) : array( 'class' => 'entry-image' );
			$image = wp_get_attachment_image( $instance['attachment_id'], $instance['custom_image_size'], false, $atts );

			if ( $instance['feature_type'] == 'page' && $instance['enable_image_link'] == 1 ) {
				printf( 
					'<a href="%s" title="%s" class="%s" target="%s" rel="%s" %s>%s</a>', 
					$link_url, 
					$page_title, 
					esc_attr( $instance['image_alignment'] ), 
					esc_attr( $instance['target_attr'] ), 
					esc_attr( $instance['rel_attr'] ), 
					$role, 
					$image 
				);
			} elseif ($instance['feature_type'] == 'custom' && $instance['enable_image_link'] == 1 ) {
				printf( 
					'<a href="%s" title="%s" class="%s" target="%s" rel="%s" %s>%s</a>', 
					esc_url( $instance['custom_link'] ), 
					esc_attr( $instance['custom_title'] ), 
					esc_attr( $instance['image_alignment'] ), 
					esc_attr( $instance['target_attr'] ), 
					esc_attr( $instance['rel_attr'] ), 
					$role, 
					$image 
				);
			} else {
				// The <span> replaces the <a> so the image alignment feature still works (we manually apply the styling to image)
				if ( $instance['image_alignment'] == 'aligncenter' ) {
					printf( 
						'<span class="%s" %s>%s</span>', 
						esc_attr( $instance['image_alignment'] ), 
						$role, 
						$image 
					);
				} else {
					printf( 
						'<span class="%s" %s>%s</span>', 
						esc_attr( $instance['image_alignment'] ), 
						$role, 
						$image 
					);
				}
			}
		}

		// Display widget title below image
		if ( $instance['widget_title_below'] ) {
			echo $widget_title;
		}

		// Display entry header (page title) below image
		if ( ! $instance['page_title_above'] ) {
			echo $entry_header;
		}
		
		if ( ! empty( $instance['show_content'] ) || ! empty( $instance['show_excerpt'] ) || ! empty( $instance['show_custom_content'] ) ) {
			
			// Set the More Link if the text exits
			if ( ! empty( $instance['more_text'] ) ) {
					
				$more_link = sprintf( 
					'<a href="%s" class="more-link" target="%s" rel="%s">%s</a>', 
					$link_url, 
					esc_attr( $instance['target_attr'] ), 
					esc_attr( $instance['rel_attr'] ), 
					genesis_a11y_more_link( esc_attr( $instance['more_text'] ) ) 
				);
			} else {
				$more_link = '';
			}

			genesis_markup(
				array(
					'open'    => '<div %s>',
					'context' => 'entry-content',
					'params'  => array(
						'is_widget' => true,
					),
				)
			);
			
			if ( $instance['feature_type'] == 'page' ) {
				
				// Display page content
				if ( ! empty( $instance['show_content'] ) ) {
					echo $page_content;
				}
				
				// Display page excerpt
				if ( ! empty( $instance['show_excerpt'] ) ) {
					if ( $instance['more_text_new_line'] ) {
						printf( 
							'<p>%s</p>',
							$page_excerpt
						);
					} else {
						printf( 
							'<p>%s%s</p>',
						 	$page_excerpt,
							$more_link
						);
					}
				}
				
			}
			
			// Display custom content
			if ( $instance['show_custom_content'] && ! empty( $instance['custom_content'] ) ) {
				if ( $instance['more_text_new_line'] ) {
					printf( 
						'<p>%s</p>',
						do_shortcode( wp_kses_post( $instance['custom_content'] ) )
					);
				} else {
					printf( 
						'<p>%s%s</p>',
						do_shortcode( wp_kses_post( $instance['custom_content'] ) ),
						$more_link
					);
				}
			}
			
			// Render the More Link on a new line
			if ( $instance['more_text_new_line'] && $more_link ) {
				printf( 
					'<div class=%s>%s</div>',
					'fpa-more-link',
					$more_link
				);
			}
			
			genesis_markup(
				array(
					'close'   => '</div>',
					'context' => 'entry-content',
					'params'  => array(
						'is_widget' => true,
					),
				)
			);
			
		}

		genesis_markup(
			array(
				'close'   => '</article>',
				'context' => 'entry',
				'params'  => array(
					'is_widget' => true,
				),
			)
		);



		echo $args['after_widget'];
	}


	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {

		$new_instance['title']     			= strip_tags( $new_instance['title'] );
		$new_instance['custom_link'] 		= strip_tags( $new_instance['custom_link'] );
		$new_instance['rel_attr'] 			= str_replace( ',', '', strip_tags( $new_instance['rel_attr'] ) );
		$new_instance['custom_image']       = strip_tags( $new_instance['custom_image'] );
		$new_instance['custom_title']       = strip_tags( $new_instance['custom_title'] );
		$new_instance['custom_content']     = $new_instance['custom_content'];
		$new_instance['more_text'] 			= strip_tags( $new_instance['more_text'] );

		return $new_instance;
	}


	/**
	 * Echo the settings update form on admin widget page.
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance ) {

		// Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		// Gets widget id prefix, very important for image uploader
		$id_prefix = $this->get_field_id('');
		?>

		<!--Widget Title Block-->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title', 'genesis-featured-page-advanced' ); ?>:
			</label>
			<input 
				type="text" 
				id="<?php echo $this->get_field_id( 'title' ); ?>" 
				name="<?php echo $this->get_field_name( 'title' ); ?>" 
				value="<?php echo esc_attr( $instance['title'] ); ?>" 
				class="widefat"
			/>
		</p>
		<p>
			<input 
				id="<?php echo $this->get_field_id( 'enable_title_link' ); ?>" 
				type="checkbox" 
				name="<?php echo $this->get_field_name( 'enable_title_link' ); ?>" 
				value="1" 
				<?php checked( $instance['enable_title_link'] ); ?>
			/>
			<label for="<?php echo $this->get_field_id( 'enable_title_link' ); ?>">
				<?php _e( 'Enable Title Link', 'genesis-featured-page-advanced' ); ?>
			</label>
		</p>
		<p>
			<input 
				id="<?php echo $this->get_field_id( 'widget_title_below' ); ?>" 
				type="checkbox" 
				name="<?php echo $this->get_field_name( 'widget_title_below' ); ?>" 
				value="1" 
				<?php checked( $instance['widget_title_below'] ); ?>
			/>
			<label for="<?php echo $this->get_field_id( 'widget_title_below' ); ?>"><?php _e( 'Position Title Below Image', 'genesis-featured-page-advanced' ); ?></label>
		</p>

		<hr class="div" />

		<!--Featured Page Selection-->
		<div class="fpa-feature-type fpa-radio">
			<label for="<?php echo $this->get_field_id( 'feature_page' ); ?>">
				<input 
					type="radio" 
					id="<?php echo $this->get_field_id( 'feature_page' ); ?>" 
					name="<?php echo $this->get_field_name( 'feature_type' ); ?>" 
					value="page" 
					<?php checked( 'page', $instance['feature_type'] ); ?>
				/>
				<span><?php _e( 'Feature a Page', 'genesis-featured-page-advanced' ); ?></span>
			</label>
			<br />
			<label for="<?php echo $this->get_field_id( 'feature_custom' ); ?>">
				<input 
					type="radio" id="<?php echo $this->get_field_id( 'feature_custom' ); ?>" 
					name="<?php echo $this->get_field_name( 'feature_type' ); ?>" 
					value="custom"  
					<?php checked( 'custom', $instance['feature_type'] ); ?> 
				/>
				<span><?php _e( 'Feature a Custom Page Link', 'genesis-featured-page-advanced' ); ?></span>
			</label>
			<br />
		</div>
		<div 
			class="<?php if ( $instance['feature_type'] != 'page' ) echo ('hidden');  ?>" 
			id="<?php echo $this->get_field_id('feature_type_page'); ?>"
		>
			<p>
				<label for="<?php echo $this->get_field_id( 'page_id' ); ?>"><?php _e( 'Select Page', 'genesis-featured-page-advanced' ); ?>:</label>
				<?php wp_dropdown_pages( array( 'name' => $this->get_field_name( 'page_id' ), 'selected' => $instance['page_id'] ) ); ?>
			</p>
		</div>
		<div 
			class="<?php if ( $instance['feature_type'] != 'custom' ) echo ('hidden');  ?>" 
			id="<?php echo $this->get_field_id('feature_type_custom'); ?>"
		>
			<p>
				<label for="<?php echo $this->get_field_id( 'custom_link' ); ?>"><?php _e( 'Custom Page Link', 'genesis-featured-page-advanced' ); ?>:</label>
				<input 
					type="text" 
					id="<?php echo $this->get_field_id( 'custom_link' ); ?>" 
					name="<?php echo $this->get_field_name( 'custom_link' ); ?>" 
					value="<?php echo esc_attr( $instance['custom_link'] ); ?>" 
					class="widefat" 
				/>
			</p>
			<p>
				<em><?php _e( 'This will direct all widget links (title, image, more text) to the custom link. Include the full path, i.e.', 'genesis-featured-page-advanced' ); ?> <code>http://</code></em>
			</p>
		</div>

		<hr class="div" />

		<!--Additional Link Options Selection-->
		<div class="fpa-link-attributes">
			<p>
				<label 
					for="<?php echo $this->get_field_id( 'target_attr' ); ?>" 
					title="<?php _e( 'Select the link target.', 'genesis-featured-page-advanced' ); ?>"
				><?php _e( 'Link', 'genesis-featured-page-advanced' ); ?> <code>target</code> <?php _e( 'Attribute', 'genesis-featured-page-advanced' ); ?>:</label>
				<select 
					id="<?php echo $this->get_field_id( 'target_attr' ); ?>" 
					name="<?php echo $this->get_field_name( 'target_attr' ); ?>"
				>
					<option value="_self" <?php selected( '_self', $instance[ 'target_attr' ] ); ?>>
						<?php _e( 'Self', 'genesis-featured-page-advanced' ); ?>
					</option>
					<option value="_blank" <?php selected( '_blank', $instance[ 'target_attr' ] ); ?>>
						<?php _e( 'Blank', 'genesis-featured-page-advanced' ); ?>
					</option>
					<option value="_parent" <?php selected( '_parent', $instance[ 'target_attr' ] ); ?>>
						<?php _e( 'Parent', 'genesis-featured-page-advanced' ); ?>
					</option>
					<option value="_top" <?php selected( '_top', $instance[ 'target_attr' ] ); ?>>
						<?php _e( 'Top', 'genesis-featured-page-advanced' ); ?>
					</option>
				</select>
			</p>
			<p>
				<label 
					for="<?php echo $this->get_field_id( 'rel_attr' ); ?>" 
					title="<?php _e( 'Enter values for the link rel attribute.', 'genesis-featured-page-advanced' ); ?>"
				><?php _e( 'Link', 'genesis-featured-page-advanced' ); ?> <code>rel</code> <?php _e( 'Values', 'genesis-featured-page-advanced' ); ?>:</label>
				<input 
					type="text" 
					id="<?php echo $this->get_field_id( 'rel_attr' ); ?>" 
					name="<?php echo $this->get_field_name( 'rel_attr' ); ?>" 
					placeholder="ex: nofollow author" 
					value="<?php echo esc_attr( $instance['rel_attr'] ); ?>"
				/>
			</p>
			<p>
				<em><?php _e( 'Enter a space separated list of link type values.', 'genesis-featured-page-advanced' ); ?></em>
			</p>
		</div>

		<hr class="div" />

		<!--Image Type Selection-->
		<div class="fpa-show-image fpa-radio">
			<label for="<?php echo $this->get_field_id( 'show_no_image' ); ?>">
				<input 
					type="radio" 
					id="<?php echo $this->get_field_id( 'show_no_image' ); ?>" 
					name="<?php echo $this->get_field_name( 'show_image' ); ?>" 
					value="1" <?php checked( 1, $instance['show_image'] ); ?> />
				<span><?php _e( 'No Image', 'genesis-featured-page-advanced' ); ?></span>
			</label>
			<br />
			<label for="<?php echo $this->get_field_id( 'show_featured_image' ); ?>">
				<input 
					type="radio" 
					id="<?php echo $this->get_field_id( 'show_featured_image' ); ?>" 
					name="<?php echo $this->get_field_name( 'show_image' ); ?>"
					value="2" 
					<?php if ( $instance['feature_type'] != 'page' ) echo ('disabled'); ?>
					<?php checked( 2, $instance['show_image'] ); ?> 
				/>
				<span 
					class="<?php if ( $instance['feature_type'] != 'page' ) echo ('fpa-disabled'); ?>" 
					id="<?php echo $this->get_field_id( 'featured_image_disable' ); ?>"
				><?php _e( 'Show Featured Image', 'genesis-featured-page-advanced' ); ?></span>
			</label>
			<br />
			<label for="<?php echo $this->get_field_id( 'show_custom_image' ); ?>">
				<input 
					type="radio" 
					id="<?php echo $this->get_field_id( 'show_custom_image' ); ?>" 
					name="<?php echo $this->get_field_name( 'show_image' ); ?>" 
					value="3" 
					<?php checked( 3, $instance['show_image'] ); ?> 
				/>
				<span><?php _e( 'Show Custom Image', 'genesis-featured-page-advanced' ); ?></span>
			</label>
		</div>

		<!--Show Featured Image-->
		<div 
			class="fpa-image-size <?php if ( $instance['show_image'] != 2 ) echo ('hidden');  ?>" 
			id="<?php echo $this->get_field_id('toggle_image_size'); ?>" 
		>
			<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'Featured Image Size', 'genesis-featured-page-advanced' ); ?>:</label>
			<select 
				id="<?php echo $this->get_field_id( 'image_size' ); ?>" 
				class="genesis-image-size-selector" 
				name="<?php echo $this->get_field_name( 'image_size' ); ?>"
			>
				<optgroup label="Built-In Sizes">
					<option value="thumbnail" <?php selected( 'thumbnail', $instance[ 'image_size' ] ); ?>>
						Thumbnail (<?php echo absint( get_option( 'thumbnail_size_w' ) ); ?>&#x000D7;<?php echo absint( get_option( 'thumbnail_size_h' ) ); ?>)
					</option>
					<option value="medium" <?php selected( 'medium', $instance[ 'image_size' ] ); ?>>
						Medium (<?php echo absint( get_option( 'medium_size_w' ) ); ?>&#x000D7;<?php echo absint( get_option( 'medium_size_h' ) ); ?>)
					</option>
					<option value="large" <?php selected( 'large', $instance[ 'image_size' ] ); ?>>
						Large (<?php echo absint( get_option( 'large_size_w' ) ); ?>&#x000D7;<?php echo absint( get_option( 'large_size_h' ) ); ?>)
					</option>
					<option value="full" <?php selected( 'full', $instance[ 'image_size' ] ); ?>>
						Full (<?php _e( 'Original Size', 'genesis-featured-page-advanced' ); ?>)
					</option>
				</optgroup>
				<optgroup label="Custom Sizes">
					<?php
					$sizes = genesis_get_additional_image_sizes();
					foreach ( (array) $sizes as $name => $size )
						echo '<option value="' . esc_attr( $name ) . '" ' . selected( $name, $instance['image_size'], FALSE ) . '>' . esc_html( $name ) . ' (' . absint( $size['width'] ) . 'x' . absint( $size['height'] ) . ')</option>';
					?>
				</optgroup>
			</select>
		</div>

		<!--Show Custom Image-->
		<div 
			class="<?php if ( $instance['show_image'] != 3 ) echo ('hidden'); ?>" 
			id="<?php echo $this->get_field_id('toggle_uploader'); ?>"
		>
			<input 
				type="submit" 
				class="button fpa-uploader-button" 
				name="<?php echo $this->get_field_name('uploader_button'); ?>" 
				id="<?php echo $this->get_field_id('uploader_button'); ?>" 
				value="<?php _e( 'Select an Image', 'genesis-featured-page-advanced' ); ?>" 
				onclick="fpa_imageUpload.uploader( '<?php echo $this->id; ?>', '<?php echo $id_prefix; ?>' ); return false;" 
			/>
			<div class="fpa-image-preview-wrapper">
				<div class="fpa-image-preview-inner">
				<?php if ( !empty( $instance['custom_image'] ) ) {?>
					<img 
						id="<?php echo $this->get_field_id('preview'); ?>" 
						src="<?php echo $instance['custom_image']; ?>" 
					/>
				<?php } else {?>
					<img 
						id="<?php echo $this->get_field_id('preview'); ?>" 
						src="<?php echo plugin_dir_url( __FILE__ ) ?>../assets/images/default.jpg" 
					/>
				<?php }?>
				</div>
			</div>
			<input 
				type="hidden" 
				id="<?php echo $this->get_field_id('attachment_id'); ?>" 
				name="<?php echo $this->get_field_name('attachment_id'); ?>" 
				value="<?php echo abs($instance['attachment_id']); ?>" 
			/>
			<input 
				type="hidden" 
				id="<?php echo $this->get_field_id('custom_image'); ?>" 
				name="<?php echo $this->get_field_name('custom_image'); ?>" 
				value="<?php echo $instance['custom_image']; ?>" 
			/>
			<p>
				<label for="<?php echo $this->get_field_id( 'custom_image_size' ); ?>"><?php _e( 'Custom Image Size', 'genesis-featured-page-advanced' ); ?>:</label>
				<select 
					id="<?php echo $this->get_field_id( 'custom_image_size' ); ?>" 
					class="genesis-image-size-selector" 
					name="<?php echo $this->get_field_name( 'custom_image_size' ); ?>"
				>
					<optgroup label="Built-In Sizes">
						<option value="thumbnail" <?php selected( 'thumbnail', $instance[ 'custom_image_size' ] ); ?>>
							Thumbnail (<?php echo absint( get_option( 'thumbnail_size_w' ) ); ?>&#x000D7;<?php echo absint( get_option( 'thumbnail_size_h' ) ); ?>)
						</option>
						<option value="medium" <?php selected( 'medium', $instance[ 'custom_image_size' ] ); ?>>
							Medium (<?php echo absint( get_option( 'medium_size_w' ) ); ?>&#x000D7;<?php echo absint( get_option( 'medium_size_h' ) ); ?>)
						</option>
						<option value="large" <?php selected( 'large', $instance[ 'custom_image_size' ] ); ?>>
							Large (<?php echo absint( get_option( 'large_size_w' ) ); ?>&#x000D7;<?php echo absint( get_option( 'large_size_h' ) ); ?>)
						</option>
						<option value="full" <?php selected( 'full', $instance[ 'custom_image_size' ] ); ?>>
							Full (<?php _e( 'Original Size', 'genesis-featured-page-advanced' ); ?>)
						</option>
					</optgroup>
					<optgroup label="Custom Sizes">
						<?php
						$sizes = genesis_get_additional_image_sizes();
						foreach ( (array) $sizes as $name => $size ) {
							echo '<option value="' . esc_attr( $name ) . '" ' . selected( $name, $instance['custom_image_size'], FALSE ) . '>';
								echo esc_html( $name ) . ' (' . absint( $size['width'] ) . 'x' . absint( $size['height'] ) . ')';
							echo '</option>';
						}
						?>
					</optgroup>
				</select>
			</p>
		</div>

		<!--Image Alignment-->
		<p 
			class="<?php if ( $instance['show_image'] == 1 ) echo ('hidden'); ?>" 
			id="<?php echo $this->get_field_id('toggle_image_alignment'); ?>"
		>
			<label for="<?php echo $this->get_field_id( 'image_alignment' ); ?>"><?php _e( 'Image Alignment', 'genesis-featured-page-advanced' ); ?>:</label>
			<select 
				id="<?php echo $this->get_field_id( 'image_alignment' ); ?>" 
				name="<?php echo $this->get_field_name( 'image_alignment' ); ?>"
			>
				<option value="alignnone">
					- <?php _e( 'None', 'genesis-featured-page-advanced' ); ?> -
				</option>
				<option value="alignleft" <?php selected( 'alignleft', $instance['image_alignment'] ); ?>>
					<?php _e( 'Left', 'genesis-featured-page-advanced' ); ?>
				</option>
				<option value="alignright" <?php selected( 'alignright', $instance['image_alignment'] ); ?>>
					<?php _e( 'Right', 'genesis-featured-page-advanced' ); ?>
				</option>
				<option value="aligncenter" <?php selected( 'aligncenter', $instance['image_alignment'] ); ?>>
					<?php _e( 'Center', 'genesis-featured-page-advanced' ); ?>
				</option>
			</select>
		</p>

		<p 
			class="<?php if ( $instance['show_image'] == 1 ) echo ('hidden'); ?>" 
			id="<?php echo $this->get_field_id('toggle_image_link'); ?>"
		>
			<input 
				id="<?php echo $this->get_field_id( 'enable_image_link' ); ?>" 
				type="checkbox" 
				name="<?php echo $this->get_field_name( 'enable_image_link' ); ?>" 
				value="1" 
				<?php checked( 1, $instance['enable_image_link'] ); ?>
			/>
			<label for="<?php echo $this->get_field_id( 'enable_image_link' ); ?>"><?php _e( 'Enable Image Link', 'genesis-featured-page-advanced' ); ?></label>
		</p>

		<hr class="div" />

		<!--Title Type Selection-->
		<div class="fpa-show-title fpa-radio">
			<label for="<?php echo $this->get_field_id( 'show_no_title' ); ?>">
				<input 
					type="radio" 
					id="<?php echo $this->get_field_id( 'show_no_title' ); ?>" 
					name="<?php echo $this->get_field_name( 'show_title' ); ?>" 
					value="0" <?php checked( 0, $instance['show_title'] ); ?>
				/>
				<span><?php _e( 'No Page Title', 'genesis-featured-page-advanced' ); ?></span>
			</label>
			<br />
			<label for="<?php echo $this->get_field_id( 'show_page_title' ); ?>">
				<input 
					type="radio" 
					id="<?php echo $this->get_field_id( 'show_page_title' ); ?>" 
					name="<?php echo $this->get_field_name( 'show_title' ); ?>" 
					value="1" 
					<?php if ( $instance['feature_type'] != 'page' ) echo ('disabled'); ?> 
					<?php checked( 1, $instance['show_title'] ); ?> 
				/>
				<span 
					class="<?php if ( $instance['feature_type'] != 'page' ) echo ('fpa-disabled'); ?>" 
					id="<?php echo $this->get_field_id( 'page_title_disable' ); ?>"
				><?php _e( 'Show Page Title', 'genesis-featured-page-advanced' ); ?></span>
			</label>
			<br />
			<label for="<?php echo $this->get_field_id( 'show_custom_title' ); ?>">
				<input 
					type="radio" 
					id="<?php echo $this->get_field_id( 'show_custom_title' ); ?>" 
					name="<?php echo $this->get_field_name( 'show_title' ); ?>" 
					value="2" 
					<?php checked( 2, $instance['show_title'] ); ?>
				/>
				<span><?php _e( 'Show Custom Page Title', 'genesis-featured-page-advanced' ); ?></span>
			</label>
		</div>

		<!--Custom Title Block-->
		<p 
			class="<?php if ( $instance['show_title'] != 2 ) echo ('hidden'); ?>" 
			id="<?php echo $this->get_field_id('toggle_custom_title'); ?>"
		>
			<label for="<?php echo $this->get_field_id( 'custom_title' ); ?>"><?php _e( 'Custom Page Title', 'genesis-featured-page-advanced' ); ?>:</label>
			<input 
				type="text" 
				id="<?php echo $this->get_field_id( 'custom_title' ); ?>" 
				name="<?php echo $this->get_field_name( 'custom_title' ); ?>" 
				value="<?php echo esc_attr( $instance['custom_title'] ); ?>" 
				class="widefat" 
			/>
		</p>

		<p 
			class="<?php if ( $instance['show_title'] == 0 ) echo ('hidden'); ?>" 
			id="<?php echo $this->get_field_id('toggle_title_link'); ?>"
		>
			<input 
				id="<?php echo $this->get_field_id( 'enable_page_title_link' ); ?>" 
				type="checkbox" name="<?php echo $this->get_field_name( 'enable_page_title_link' ); ?>" 
				value="1" 
				<?php checked( 1, $instance['enable_page_title_link'] ); ?>
			/>
			<label for="<?php echo $this->get_field_id( 'enable_page_title_link' ); ?>"><?php _e( 'Enable Page Title Link', 'genesis-featured-page-advanced' ); ?></label>
		</p>
		<p 
			class="<?php if ( $instance['show_title'] == 0 ) echo ('hidden'); ?>" 
			id="<?php echo $this->get_field_id('toggle_title_above'); ?>"
		>
			<input 
				id="<?php echo $this->get_field_id( 'page_title_above' ); ?>" 
				type="checkbox" name="<?php echo $this->get_field_name( 'page_title_above' ); ?>" 
				value="1" 
				<?php checked( $instance['page_title_above'] ); ?> 
			/>
			<label for="<?php echo $this->get_field_id( 'page_title_above' ); ?>"><?php _e( 'Position Page Title Above Image', 'genesis-featured-page-advanced' ); ?></label>
		</p>

		<hr class="div" />

		<!--Featured Page Specific Settings - Hide if using Custom Link-->
		<div 
			class="<?php if ( $instance['feature_type'] != 'page' ) echo ('hidden');  ?>" 
			id="<?php echo $this->get_field_id('feature_type_page_settings'); ?>" 
		>
		
			<!--Page Content Block-->
			<p class="fpa-toggle-content-limit">
				<input 
					id="<?php echo $this->get_field_id( 'show_content' ); ?>" 
					type="checkbox" name="<?php echo $this->get_field_name( 'show_content' ); ?>" 
					value="1" 
					<?php checked( $instance['show_content'] ); ?>
				/>
				<label for="<?php echo $this->get_field_id( 'show_content' ); ?>"><?php _e( 'Show Page Content', 'genesis-featured-page-advanced' ); ?></label>
			</p>
			<p 
				class="<?php if ( $instance['show_content'] != 1 ) echo ('hidden'); ?>" 
				id="<?php echo $this->get_field_id('toggle_content_limit'); ?>"
			>
				<label for="<?php echo $this->get_field_id( 'content_limit' ); ?>"><?php _e( 'Content Character Limit', 'genesis-featured-page-advanced' ); ?>:</label>
				<input 
					type="text" 
					id="<?php echo $this->get_field_id( 'content_limit' ); ?>" 
					name="<?php echo $this->get_field_name( 'content_limit' ); ?>" 
					value="<?php echo esc_attr( $instance['content_limit'] ); ?>" 
					size="3" 
				/>
			</p>

			<!--Enable Page Excerpt-->
			<p>
				<input 
					id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" 
					type="checkbox" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" 
					value="1" 
					<?php checked( $instance['show_excerpt'] ); ?>
				/>
				<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>"><?php _e( 'Show Page Excerpt', 'genesis-featured-page-advanced' ); ?></label>
			</p>
		</div>

		<!--Custom Content Block-->
		<p class="fpa-toggle-custom-content">
			<input 
				id="<?php echo $this->get_field_id( 'show_custom_content' ); ?>" 
				type="checkbox" name="<?php echo $this->get_field_name( 'show_custom_content' ); ?>" 
				value="1" 
				<?php checked( $instance['show_custom_content'] ); ?> 
			/>
			<label for="<?php echo $this->get_field_id( 'show_custom_content' ); ?>"><?php _e( 'Show Custom Content', 'genesis-featured-page-advanced' ); ?></label>
		</p>
		<p 
			class="<?php if ( $instance['show_custom_content'] != 1 ) echo ('hidden'); ?>" 
			id="<?php echo $this->get_field_id('toggle_custom_content'); ?>"
		>
			<label for="<?php echo $this->get_field_id( 'custom_content' ); ?>"><?php _e( 'Custom Content', 'genesis-featured-page-advanced' ); ?>:</label>
			<br />
			<textarea 
				rows="4" 
				id="<?php echo $this->get_field_id( 'custom_content' ); ?>" 
				name="<?php echo $this->get_field_name( 'custom_content' ); ?>" 
				class="widefat" 
				style="max-width: 100%"
			><?php echo esc_html( $instance['custom_content'] ); ?></textarea>
		</p>

		<hr class="div" />

		<!--Read More Button/Text-->
		<p>
			<label 
				for="<?php echo $this->get_field_id( 'more_text' ); ?>" 
				title="<?php _e( 'Leave empty for no More Text.', 'genesis-featured-page-advanced' ); ?>"
			><?php _e( 'More Text', 'genesis-featured-page-advanced' ); ?>:</label>
			<input 
				type="text" 
				id="<?php echo $this->get_field_id( 'more_text' ); ?>" 
				name="<?php echo $this->get_field_name( 'more_text' ); ?>" 
				value="<?php echo esc_attr( $instance['more_text'] ); ?>" 
			/>
		</p>
		<p class="fpa-last">
			<input 
				id="<?php echo $this->get_field_id( 'more_text_new_line' ); ?>" 
				type="checkbox" name="<?php echo $this->get_field_name( 'more_text_new_line' ); ?>" 
				value="1" 
				<?php checked( $instance['more_text_new_line'] ); ?> 
			/>
			<label 
				for="<?php echo $this->get_field_id( 'more_text_new_line' ); ?>" 
				title="<?php _e( 'Inserts More Text on a new line wrapped in a <div> tag with the class .fpa-more-text for styling purposes.', 'genesis-featured-page-advanced' ); ?>"
			><?php _e( 'Insert More Text on a New Line', 'genesis-featured-page-advanced' ); ?></label>
		</p>
		<?php
	}


	/**
	 * Enqueue Admin scripts and styles
	 */
	function fpa_admin_scripts_enqueue( $hook ) {

		// Do no enqueue scripts & styles if we are not on either the Widget or Customizer pages
		// Updated in version 1.9.3 to address conflict with Page Builder by SiteOrigin plugin
		if ( 'widgets.php' == $hook || 'customize.php' == $hook || defined( 'SITEORIGIN_PANELS_VERSION' ) ) {

			// Enqueues all media scripts so we can use the media uploader
			wp_enqueue_media();

			wp_register_script( 'fpa_admin_scripts', plugin_dir_url( __FILE__ ) . '../assets/js/admin-scripts.js', array( 'jquery' ) );
			wp_enqueue_script( 'fpa_admin_scripts' );

			wp_localize_script( 'fpa_admin_scripts', 'fpa_localize_admin_scripts', array(
        		'media_title' => __( 'Choose or Upload an Image', 'genesis-featured-page-advanced' ),
        		'media_button' => __( 'Use Selected Image', 'genesis-featured-page-advanced' )
    		) );

			wp_register_style( 'fpa_admin_styles', plugin_dir_url( __FILE__ ) . '../assets/css/admin-styles.css' );
			wp_enqueue_style( 'fpa_admin_styles' );

		} else {
			return;
		}
	}
}
