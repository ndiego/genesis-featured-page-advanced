jQuery(document).ready(function($){


	//Image Uploader function	  	
  	fpa_imageUpload = {

		// Call this from the upload button to initiate the upload frame.
		uploader : function( widget_id, widget_id_string ) {

			var frame = wp.media({
				title : 'Choose or Upload an Image',
				multiple : false,
				library : { type : 'image' }, //only can upload images
				button : { text : 'Use Selected Image' }
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
  	
  	 //Show and hide Page Link input on Show Title selection
  	 $(document).on( 'click', '.fpa-toggle-page-link input', function() { 
  	 
  	 	var widget_id = $(this).attr( 'id' );
  	 	var widget_id_prefix = widget_id.split( '-' ).slice( 0, 5 ).join( '-' );  
  	 	
  	 	$( '#' + widget_id_prefix + '-toggle_page_link' ).toggle( this.checked );
  	 
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