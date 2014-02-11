<?php
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
	function __construct() {

		$this->defaults = array(
			'title'           			=> '',
			'enable_title_link'			=> 0,
			'page_id'         			=> '',
			'show_image'				=> 1,
			'image_alignment' 			=> '',
			'enable_image_link'			=> 0,
			'custom_image'				=> '',
			'attachment_id'				=> 0,
			'image_size'      			=> '',
			'show_title'      			=> 0,
			'enable_page_title_link'	=> 0,
			'show_content'    			=> 0,
			'show_excerpt'				=> 0,
			'show_custom_content'   	=> 0,
			'custom_content'  			=> '',
			'content_limit'   			=> '',
			'more_text'       			=> '',
		);

		$widget_ops = array(
			'classname'   => 'featured-content featuredpage',
			'description' => 'Displays featured page with thumbnails',
		);

		$control_ops = array(
			'id_base' => 'featured-page-advanced',
			'width'   => 200,
			'height'  => 250,
		);

		parent::__construct( 'featured-page-advanced', 'Genesis - Featured Page Advanced', $widget_ops, $control_ops );

	}


	/**
	 * Echo the widget content on the frontend.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget( $args, $instance ) {

		global $wp_query;

		extract( $args );

		// Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $before_widget;

		// Display widget title
		if ( ! empty( $instance['title'] ) && $instance['enable_title_link'] == 1 ) {
			echo $before_title;
			printf( '<a href="%s" title="%s">%s</a>', get_permalink( $instance['page_id'] ), get_the_title( $instance['page_id'] ), apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) );
			echo $after_title;
		} elseif ( ! empty( $instance['title'] ) ) {
			echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
		}

		$wp_query = new WP_Query( array( 'page_id' => $instance['page_id'] ) );

		if ( have_posts() ) : while ( have_posts() ) : the_post();

			genesis_markup( array(
				'html5'   => '<article %s>',
				'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
				'context' => 'entry',
			) );

			$image = genesis_get_image( array( 
				'format' => 'html', 
				'size' => $instance['image_size'],
				'context' => 'featured-page-widget',
				'attr'    => genesis_parse_attr( 'entry-image-widget' ),
			) );
			
			// Display featured image
			if ( $instance['show_image'] == 2 && $image ) {
				if ( $instance['enable_image_link'] == 1 ) {
					printf( '<a href="%s" title="%s" class="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), esc_attr( $instance['image_alignment'] ), $image );
				} else {
					echo '<img src="' . $image .'"/>';
				}
			}
			
			// Display custom image
			if ( $instance['show_image'] == 3 ) {
				if ( $instance['enable_image_link'] == 1 ) {
					printf( '<a href="%s" title="%s" class="%s"><img src="%s"/></a>', get_permalink(), the_title_attribute( 'echo=0' ), esc_attr( $instance['image_alignment'] ), $instance['custom_image'] );
				} else {
					echo '<img src="' . $instance['custom_image'] .'"/></a>';
				}
			}
			
			// Display page title
			if ( ! empty( $instance['show_title'] ) && $instance['enable_page_title_link'] == 1 ) {
				if ( genesis_html5() ) {
					printf( '<header class="entry-header"><h2 class="entry-title"><a href="%s" title="%s">%s</a></h2></header>', get_permalink(), the_title_attribute( 'echo=0' ), get_the_title() );
				} else {
					printf( '<h2><a href="%s" title="%s">%s</a></h2>', get_permalink(), the_title_attribute( 'echo=0' ), get_the_title() );
				}
			} elseif ( ! empty( $instance['show_title'] ) ) {
				if ( genesis_html5() ) {
					printf( '<header class="entry-header"><h2 class="entry-title">%s</h2></header>', get_the_title() );
				} else {
					printf( '<h2>%s</h2>', get_the_title() );
				}
			}
			
			
			// Display page content
			if ( ! empty( $instance['show_content'] ) ) {
				
				echo genesis_html5() ? '<div class="entry-content">' : '';

				if ( empty( $instance['content_limit'] ) ) {
					the_content( $instance['more_text'] );
				} else {
					the_content_limit( (int) $instance['content_limit'], esc_html( $instance['more_text'] ) );
				}
				
				echo genesis_html5() ? '</div>' : '';

			}
			
			// Display page excerpt
			if ( ! empty( $instance['show_excerpt'] ) ) {
				
				echo genesis_html5() ? '<div class="entry-content">' : '';
				
				echo '<p>' . get_the_excerpt() . ' ';
				if ( ! empty( $instance['more_text'] ) ) {
					printf( '<a href="%s" class="more-link">%s</a>', get_permalink(), esc_attr( $instance['more_text'] ) );
				}
				echo '</p>';
				
				echo genesis_html5() ? '</div>' : '';
				
			}
			
			// Display custom content
			if ( $instance['show_custom_content'] == 1 && ! empty( $instance['custom_content'] ) ) {
				
				echo genesis_html5() ? '<div class="entry-content">' : '';
				
				echo '<p>' . $instance['custom_content'] . ' ';
				if ( ! empty( $instance['more_text'] ) ) {
					printf( '<a href="%s" class="more-link">%s</a>', get_permalink(), esc_attr( $instance['more_text'] ) );
				}
				echo '</p>';
				
				echo genesis_html5() ? '</div>' : '';
				
			}

			genesis_markup( array(
				'html5' => '</article>',
				'xhtml' => '</div>',
			) );

			endwhile;
		endif;

		// Restore original query
		wp_reset_query();

		echo $after_widget;

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

		$new_instance['title']     			 = strip_tags( $new_instance['title'] );
		$new_instance['custom_image']        = strip_tags( $new_instance['custom_image'] );
		$new_instance['custom_content']      = strip_tags( $new_instance['custom_content'] );
		$new_instance['more_text'] 			 = strip_tags( $new_instance['more_text'] );
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

		//Gets widget id prefix, very important for image uploader
		$id_prefix = $this->get_field_id('');

		?>
		
		<!--Widget Title Block-->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo ('Title'); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'enable_title_link' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'enable_title_link' ); ?>" value="1" <?php checked( $instance['enable_title_link'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'enable_title_link' ); ?>"><?php echo ('Enable Title Link'); ?></label>	
		</p>
		
		<!--Featured Page Selection-->
		<p>
			<label for="<?php echo $this->get_field_id( 'page_id' ); ?>"><?php echo ('Page'); ?>:</label>
			<?php wp_dropdown_pages( array( 'name' => $this->get_field_name( 'page_id' ), 'selected' => $instance['page_id'] ) ); ?>
		</p>

		<hr class="div" />
		
		<!--Image Type Selection-->
		<div class="fpa-show-image">
			<label for="<?php echo $this->get_field_id( 'show_no_image' ); ?>">
				<input type="radio" id="<?php echo $this->get_field_id( 'show_no_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="1" <?php checked( 1, $instance['show_image'] ); ?> />
				<span>No Image</span>
			</label><br />
			<label for="<?php echo $this->get_field_id( 'show_featured_image' ); ?>">
				<input type="radio" id="<?php echo $this->get_field_id( 'show_featured_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="2"  <?php checked( 2, $instance['show_image'] ); ?> />
				<span>Show Featured Image</span>
			</label><br />
			<label for="<?php echo $this->get_field_id( 'show_custom_image' ); ?>">
				<input type="radio" id="<?php echo $this->get_field_id( 'show_custom_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="3" <?php checked( 3, $instance['show_image'] ); ?> />
				<span>Show Custom Image</span>
			</label>
		</div>
		
		<!--Show Featured Image-->
		<div class="fpa-image-size <?php if ( $instance['show_image'] != 2 ) echo ('hidden');  ?>" id="<?php echo $this->get_field_id('toggle_image_size'); ?>" >
			<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php echo ('Image Size'); ?>:</label>
			<select id="<?php echo $this->get_field_id( 'image_size' ); ?>" class="genesis-image-size-selector" name="<?php echo $this->get_field_name( 'image_size' ); ?>">
				<option value="thumbnail">thumbnail (<?php echo absint( get_option( 'thumbnail_size_w' ) ); ?>x<?php echo absint( get_option( 'thumbnail_size_h' ) ); ?>)</option>
				<option value="medium">medium (<?php echo absint( get_option( 'medium_size_w' ) ); ?>x<?php echo absint( get_option( 'medium_size_h' ) ); ?>)</option>
				<option value="large">large (<?php echo absint( get_option( 'large_size_w' ) ); ?>x<?php echo absint( get_option( 'large_size_h' ) ); ?>)</option>
				<?php
				$sizes = genesis_get_additional_image_sizes();
				foreach ( (array) $sizes as $name => $size )
					echo '<option value="' . esc_attr( $name ) . '" ' . selected( $name, $instance['image_size'], FALSE ) . '>' . esc_html( $name ) . ' (' . absint( $size['width'] ) . 'x' . absint( $size['height'] ) . ')</option>';
				?>
			</select>
		</div>
		
		<!--Show Custom Image-->		
		<div class="<?php if ( $instance['show_image'] != 3 ) echo ('hidden'); ?>" id="<?php echo $this->get_field_id('toggle_uploader'); ?>"  >
			<input type="submit" class="button fpa-uploader-button" name="<?php echo $this->get_field_name('uploader_button'); ?>" id="<?php echo $this->get_field_id('uploader_button'); ?>" value="<?php _e( 'Select an Image', 'genesis' ); ?>" onclick="fpa_imageUpload.uploader( '<?php echo $this->id; ?>', '<?php echo $id_prefix; ?>' ); return false;" />
			<div class="fpa-image-preview-wrapper">
				<div class="fpa-image-preview-inner">
				<?php if ( !empty( $instance['custom_image'] ) ) {?>
					<img id="<?php echo $this->get_field_id('preview'); ?>" src="<?php echo $instance['custom_image']; ?>" /> 
				<?php } else {?>
					<img id="<?php echo $this->get_field_id('preview'); ?>" src="<?php echo plugin_dir_url( __FILE__ ) ?>../images/default.jpg" /> 
				<?php }?>
				</div>
			</div>
			<input type="hidden" id="<?php echo $this->get_field_id('attachment_id'); ?>" name="<?php echo $this->get_field_name('attachment_id'); ?>" value="<?php echo abs($instance['attachment_id']); ?>" />
			<input type="hidden" id="<?php echo $this->get_field_id('custom_image'); ?>" name="<?php echo $this->get_field_name('custom_image'); ?>" value="<?php echo $instance['custom_image']; ?>" />
		</div>

		<!--Image Alignment-->
		<p class="<?php if ( $instance['show_image'] == 1 ) echo ('hidden'); ?>" id="<?php echo $this->get_field_id('toggle_image_alignment'); ?>" >
			<label for="<?php echo $this->get_field_id( 'image_alignment' ); ?>"><?php echo ('Image Alignment'); ?>:</label>
			<select id="<?php echo $this->get_field_id( 'image_alignment' ); ?>" name="<?php echo $this->get_field_name( 'image_alignment' ); ?>">
				<option value="alignnone">- <?php echo ('None'); ?> -</option>
				<option value="alignleft" <?php selected( 'alignleft', $instance['image_alignment'] ); ?>><?php echo ('Left'); ?></option>
				<option value="alignright" <?php selected( 'alignright', $instance['image_alignment'] ); ?>><?php echo ('Right'); ?></option>
			</select>
		</p>
		
		<p class="<?php if ( $instance['show_image'] == 1 ) echo ('hidden'); ?>" id="<?php echo $this->get_field_id('toggle_image_link'); ?>">
			<input id="<?php echo $this->get_field_id( 'enable_image_link' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'enable_image_link' ); ?>" value="1" <?php checked( 1, $instance['enable_image_link'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'enable_image_link' ); ?>"><?php echo ('Enable Image Link'); ?></label>
		</p>
		
		<hr class="div" />

		<!--Page Title Block-->
		<p class="fpa-toggle-page-link">
			<input id="<?php echo $this->get_field_id( 'show_title' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_title' ); ?>" value="1" <?php checked( $instance['show_title'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php echo ('Show Page Title'); ?></label>
		</p>
		<p class="<?php if ( $instance['show_title'] != 1 ) echo ('hidden'); ?>" id="<?php echo $this->get_field_id('toggle_page_link'); ?>">
			<input id="<?php echo $this->get_field_id( 'enable_page_title_link' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'enable_page_title_link' ); ?>" value="1" <?php checked( 1, $instance['enable_page_title_link'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'enable_page_title_link' ); ?>"><?php echo ('Enable Page Title Link'); ?></label>
		</p>
		
		<hr class="div" />
		
		<!--Page Content Block-->
		<p class="fpa-toggle-content-limit">
			<input id="<?php echo $this->get_field_id( 'show_content' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_content' ); ?>" value="1" <?php checked( $instance['show_content'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'show_content' ); ?>"><?php echo ('Show Page Content'); ?></label>
		</p>
		<p class="<?php if ( $instance['show_content'] != 1 ) echo ('hidden'); ?>" id="<?php echo $this->get_field_id('toggle_content_limit'); ?>">
			<label for="<?php echo $this->get_field_id( 'content_limit' ); ?>"><?php echo ('Content Character Limit'); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'content_limit' ); ?>" name="<?php echo $this->get_field_name( 'content_limit' ); ?>" value="<?php echo esc_attr( $instance['content_limit'] ); ?>" size="3" />
		</p>
		
		<!--Enable Page Excerpt-->
		<p>
			<input id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" value="1" <?php checked( $instance['show_excerpt'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>"><?php echo ('Show Page Excerpt'); ?></label>
		</p>
		
		<!--Custom Content Block-->
		<p class="fpa-toggle-custom-content">
			<input id="<?php echo $this->get_field_id( 'show_custom_content' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_custom_content' ); ?>" value="1" <?php checked( $instance['show_custom_content'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'show_custom_content' ); ?>"><?php echo ('Show Custom Content'); ?></label>
		</p>
		<p class="<?php if ( $instance['show_custom_content'] != 1 ) echo ('hidden'); ?>" id="<?php echo $this->get_field_id('toggle_custom_content'); ?>">
			<label for="<?php echo $this->get_field_id( 'custom_content' ); ?>"><?php echo ('Custom Content'); ?>:</label><br />
			<textarea rows="4" id="<?php echo $this->get_field_id( 'custom_content' ); ?>" name="<?php echo $this->get_field_name( 'custom_content' ); ?>" class="widefat" style="max-width: 100%" ><?php echo esc_attr( $instance['custom_content'] ); ?></textarea>
		</p>

		<hr class="div" />

		<!--Read More Button/Text-->
		<p class="fpa-read-more">
			<label for="<?php echo $this->get_field_id( 'more_text' ); ?>"><?php echo ('More Text'); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'more_text' ); ?>" name="<?php echo $this->get_field_name( 'more_text' ); ?>" value="<?php echo esc_attr( $instance['more_text'] ); ?>" />
		</p>
		<?php

	}

}


add_action('admin_head', 'fpa_css_head');
/**
 * Admin header css
 */
function fpa_css_head(){
?>
	<style>
		.fpa-show-image label {
			display: inline-block;
			margin: .5em 0;
		}
		.fpa-image-size {
			margin: 1em 0 0;
		}
		.fpa-image-preview-wrapper {
			border: 1px solid #e5e5e5;
			margin: 1em 0;
			display: block;
		}
		.fpa-image-preview-inner {
			border: 1px solid #e5e5e5;
			margin: .5em;
			display: block;	
		}
		.fpa-image-preview-inner img {
			vertical-align: top;
			width: 100%;
		}
		.fpa-uploader-button {
			margin: .75em 0 .25em !important;
			width: 100%;
		}
		.fpa-read-more {
			margin-bottom: 2em;
		}
	</style>
<?php
}


add_action( 'admin_enqueue_scripts', 'fpa_admin_scripts_enqueue' );
/**
 * Admin js enqueue - allows us to show/hide certain fields and upload custom image
 */
function fpa_admin_scripts_enqueue() {
	
	//Enqueues all media scripts so we can use the media uploader
    wp_enqueue_media(); 
    
    wp_register_script( 'fpa-admin-scripts', plugin_dir_url( __FILE__ ) . 'fpa-admin-scripts.js', array( 'jquery' ) );
	wp_enqueue_script( 'fpa-admin-scripts' );

}


