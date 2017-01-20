jQuery( function ( $ ) {

	$( document ).on( 'change', '.pngx-template-chooser, .pngx-variety-select', function ( e ) {

		e.preventDefault();

		var $option = $( this ).find( 'option:selected' ).val();

		var $ajax_field = $( this ).data( 'toggleAjax_field' );
		var $ajax_field_id = $( this ).data( 'toggleAjax_field_id' );
		var $ajax_action = $( this ).data( 'toggleAjax_action' );

		if ( ! $ajax_field && ! $ajax_field_id && ! $ajax_action ) {
			console.log('missing');
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


					var editors = document.getElementsByClassName( "pngx-ajax-wp-editor" );
					var selector;
					for ( var i = 0; i < editors.length; i++ ) {

						//console.log( editors[i], editors[i].id );

						selector = '#' + editors[i].id;

						//console.log(selector);

						$( selector ).wp_editor( false, 'apid' + i, false );

						//console.log( 'id', i );
						if ( 0 == i ) {
							//console.log( 'id2', i, editors[i].id );
							//	selector = '#' + editors[i].id;
						} else {
							//console.log( 'id3', i, editors[i].id );
							//	selector = selector + ', #' + editors[i].id;
						}
						//console.log( selector );
					}
					var image_upload = $( ".pngx-meta-template-wrap .pngx-meta-field.field-image" );
					var selector_img;
					for ( i = 0; i < image_upload.length; i++ ) {
						selector_img = $( image_upload[i] ).find( 'input' ).attr( 'id' );
						new PNGX__Media( $, selector_img );
					}

					$( $ajax_field + ' .pngx-color-picker' ).wpColorPicker();

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