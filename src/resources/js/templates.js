jQuery( function ( $ ) {

	$( document ).on( 'change', '.pngx-template-chooser, .pngx-variety-select', function ( e ) {

		e.preventDefault();

		var $option = $( this ).find( 'option:selected' ).val();

		var $ajax_field = $( this ).data( 'toggleAjax_field' );
		var $ajax_field_id = $( this ).data( 'toggleAjax_field_id' );
		var $ajax_action = $( this ).data( 'toggleAjax_action' );

		if ( ! $ajax_field && ! $ajax_field_id && ! $ajax_action ) {
			//console.log('missing');
			return;
		}

		$( $ajax_field ).html( 'loading' );

		$.ajax( {
			url: pngx_admin_ajax.ajaxurl,
			type: 'post',
			cache: false,
			dataType: 'json',
			data: {
				nonce: pngx_admin_ajax.nonce,
				post_id: pngx_admin_ajax.post_id,
				field: $ajax_field_id,
				option: $option,
				action: $ajax_action
			},
			success: function ( results ) {

				//console.log(results);

				if ( results.success ) {

					//console.log(JSON.parse( results.data ) );

					$( $ajax_field ).html( JSON.parse( results.data ) );

					// Init Visual Editors
					pngx_admin_fields_init.visual_editor();

					// Init Image Fields
					var image_upload = $( $ajax_field + ' .pngx-meta-field.field-image' );
					var selector_img;
					for ( i = 0; i < image_upload.length; i++ ) {
						selector_img = $( image_upload[i] ).find( 'input' ).attr( 'id' );
						new PNGX__Media( $, selector_img );
					}

					// Init Color Pickers
					$( $ajax_field + ' .pngx-color-picker' ).wpColorPicker();

					//Init Icon Pickers
					$( $ajax_field + ' .pngx-icon-picker' ).iconpicker();

				}
			},
			failure: function ( results ) {

					console.log('failed');
				//console.log(results);

			}

		} );

	} );

} );