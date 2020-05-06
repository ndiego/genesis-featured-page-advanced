jQuery(document).ready(function($){

	//Image Uploader function	  	
  	fpa_imageUpload = {

		// Call this from the upload button to initiate the upload frame.
		uploader : function( widget_id, widget_id_string ) {

			var frame = wp.media({
				title : fpa_localize_admin_scripts.media_title,
				multiple : false,
				library : { type : 'image' }, //only can upload images
				button : { text : fpa_localize_admin_scripts.media_button }
			});

			// Handle results from media manager
			frame.on( 'close', function( ) {
				var attachments = frame.state().get( 'selection' ).toJSON();
				fpa_imageUpload.render( widget_id, widget_id_string, attachments[0] );
			});

			frame.open();
			return false;
		},

		// Output Image preview and populate widget form
		render : function( widget_id, widget_id_string, attachment ) {
			$( "#" + widget_id_string + 'preview' ).attr( 'src', attachment.url );
			$( "#" + widget_id_string + 'attachment_id' ).val( attachment.id );
			$( "#" + widget_id_string + 'custom_image' ).val( attachment.url );			
		},

	};
	
	/*
	Show and hide different image options based on selection
	
	- get the id from the selected input
  	- get the prefix that corresponds to the widget
  	- lookup the target that has id = widget_prefix . toggle_uploader and toggle
  	
  	Note: Use of "document" is not very efficient, but need to use a static element or else breaks on ajax "save"
  	*/
	$(document).on( 'click', '.fpa-show-image input', function() {

  	 	var input_val = $(this).val();	
  	 	var widget_id = $(this).attr( 'id' );
  	 	//returns the widget_prefix from the id
  	 	var widget_id_prefix = widget_id.split( '-' ).slice( 0, 5 ).join( '-' );  
  	 	
  	 	if ( input_val == 3 ) {
  	 		$( '#' + widget_id_prefix + '-toggle_uploader' ).show();
  	 		$( '#' + widget_id_prefix + '-toggle_image_size' ).hide();
  	 		$( '#' + widget_id_prefix + '-toggle_image_alignment' ).show();
  	 		$( '#' + widget_id_prefix + '-toggle_image_link' ).show();
  	 	} else if ( input_val == 2 ) {
  	 		$( '#' + widget_id_prefix + '-toggle_uploader' ).hide();
  	 		$( '#' + widget_id_prefix + '-toggle_image_size' ).show();
  	 		$( '#' + widget_id_prefix + '-toggle_image_alignment' ).show();
  	 		$( '#' + widget_id_prefix + '-toggle_image_link' ).show();
  	 	} else if ( input_val == 1 ) {
  	 	  	$( '#' + widget_id_prefix + '-toggle_uploader' ).hide();
  	 	  	$( '#' + widget_id_prefix + '-toggle_image_size' ).hide();
  	 		$( '#' + widget_id_prefix + '-toggle_image_alignment' ).hide();
  	 		$( '#' + widget_id_prefix + '-toggle_image_link' ).hide();
  	 	}		
  	});
  	 
	// Show and hide different page title options based on selection
	$(document).on( 'click', '.fpa-show-title input', function() {

		var input_val = $(this).val();	
		var widget_id = $(this).attr( 'id' );
		//returns the widget_prefix from the id
		var widget_id_prefix = widget_id.split( '-' ).slice( 0, 5 ).join( '-' );  
	
		if ( input_val == 2 ) {
			$( '#' + widget_id_prefix + '-toggle_custom_title' ).show();
			$( '#' + widget_id_prefix + '-toggle_title_link' ).show();
			$( '#' + widget_id_prefix + '-toggle_title_above' ).show();
		} else if ( input_val == 1 ) {
			$( '#' + widget_id_prefix + '-toggle_custom_title' ).hide();
			$( '#' + widget_id_prefix + '-toggle_title_link' ).show();
			$( '#' + widget_id_prefix + '-toggle_title_above' ).show();
		} else if ( input_val == 0 ) {
			$( '#' + widget_id_prefix + '-toggle_custom_title' ).hide();
			$( '#' + widget_id_prefix + '-toggle_title_link' ).hide();
			$( '#' + widget_id_prefix + '-toggle_title_above' ).hide();
		}	
	});
  	 
	//Show and hide feature selections
	$(document).on( 'click', '.fpa-feature-type input', function() {

  	 	var input_val = $(this).val();	
  	 	var widget_id = $(this).attr( 'id' );
  	 	//returns the widget_prefix from the id
  	 	var widget_id_prefix = widget_id.split( '-' ).slice( 0, 5 ).join( '-' );  
  	 	
  	 	if ( input_val == 'page' ) {
  	 		$( '#' + widget_id_prefix + '-feature_type_page' ).show();
  	 		$( '#' + widget_id_prefix + '-feature_type_page_settings' ).show();
  	 		$( '#' + widget_id_prefix + '-show_featured_image' ).attr( 'disabled', false );
  	 		$( '#' + widget_id_prefix + '-show_page_title' ).attr( 'disabled', false );
  	 		$( '#' + widget_id_prefix + '-featured_image_disable' ).removeClass( 'fpa-disabled' );
  	 		$( '#' + widget_id_prefix + '-page_title_disable' ).removeClass( 'fpa-disabled' );
  	 		$( '#' + widget_id_prefix + '-feature_type_custom' ).hide();
  	 	} else if ( input_val == 'custom' ) {
  	 		$( '#' + widget_id_prefix + '-feature_type_page' ).hide();
  	 		$( '#' + widget_id_prefix + '-feature_type_page_settings' ).hide();
  	 		$( '#' + widget_id_prefix + '-show_featured_image' ).attr( 'disabled', true );
			$( '#' + widget_id_prefix + '-show_page_title' ).attr( 'disabled', true );
  	 		$( '#' + widget_id_prefix + '-featured_image_disable' ).addClass( 'fpa-disabled' );
  	 		$( '#' + widget_id_prefix + '-page_title_disable' ).addClass( 'fpa-disabled' );
  	 		$( '#' + widget_id_prefix + '-feature_type_custom' ).show();
  	 	}
  	 });
  	
  	 //Show and hide Page Link input on Show Title selection
  	 $(document).on( 'click', '.fpa-toggle-page-settings input', function() { 
  	 
  	 	var widget_id = $(this).attr( 'id' );
  	 	var widget_id_prefix = widget_id.split( '-' ).slice( 0, 5 ).join( '-' );  
  	 	
  	 	$( '#' + widget_id_prefix + '-toggle_page_settings' ).toggle( this.checked );
  	 });
  	
  	//Show and hide Custom Content on Show Custom Content selection
  	 $(document).on( 'click', '.fpa-toggle-custom-content input', function() {
	
  	 	var widget_id = $(this).attr( 'id' );
  	 	var widget_id_prefix = widget_id.split( '-' ).slice( 0, 5 ).join( '-' );  
  	 	
  	 	$( '#' + widget_id_prefix + '-toggle_custom_content' ).toggle( this.checked );
  	 });
  	 
  	 //Show and hide Character Limit input on Show Page Content selection
  	 $(document).on( 'click', '.fpa-toggle-content-limit input', function() {
  	 
  	 	var widget_id = $(this).attr( 'id' );
  	 	var widget_id_prefix = widget_id.split( '-' ).slice( 0, 5 ).join( '-' );  
  	 	
  	 	$( '#' + widget_id_prefix + '-toggle_content_limit' ).toggle( this.checked );
  	 	
  	 });

});