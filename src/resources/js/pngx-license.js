/*
 * Plugin Engine License Saving
 */
var pngx_update_license = pngx_update_license || {};
(function ( $, obj ) {
	'use strict';

	obj.fields = '';

	obj.init = function () {

		obj.license();

	};

	/*
	 * Update License on Plugin List
	 */
	obj.license = function () {

		$( '.pngx-list-license-button' ).on( 'click', function ( e ) {

			e.preventDefault();

			var field_wrap = $( this ).closest( '.pngx-license-list-key-wrap' );

			//console.log( form_wrap );

			setTimeout( function () {

				obj.start_spin( field_wrap );

				var params = {
					action: 'pngx_license_update',
					pngx_license_nonce: pngx_license.nonce,
					license_inputs: $( field_wrap ).find( ' .pngx-license-field' ).serialize()
				};

				obj.update_license( params, field_wrap );

			}, 500 );

		} );

	};


	obj.update_license = function ( params, field_wrap ) {

		if ( params ) {

			var msg, status, button, action;

			$.post(
				pngx_license.ajaxurl,
				params,
				function ( results ) {

					console.log( results );

					// set action
					action = results.data.action;

					if ( 'deactivate_license' === action ) {
						// License is Active
						status = '<span class="pngx-success-msg">' + results.data.license_status + '</span>';

					} else if ( 'activate_license' === action ) {
						// License is Not Active
						status = '<span class="pngx-error-msg">' + results.data.license_status + '</span>';

					}
					// Set action for license
					$( field_wrap ).find( '.pngx_license_action' ).val( action );

					//Set status of License
					$( field_wrap ).find( '.pngx-license-field-msg' ).html( status );

					button = results.data.button;
					$( field_wrap ).find( '.pngx-list-license-button' ).text( button );

					if ( results.success ) {

						console.log( 'success' );

						msg = '<span class="pngx-success-msg">' + results.data.message + '</span>';
						$( field_wrap ).find( '.pngx-license-field-result-msg' ).html( msg ).fadeIn();

					} else {

						msg = '<span class="pngx-error-msg">' + results.data.message + '</span>';
						$( field_wrap ).find( '.pngx-license-field-result-msg' ).html( msg ).fadeIn();

					}

				},
				'json'
			)
				.complete( function () {

					obj.stop_spin( field_wrap );

				} );

		} else {

			obj.stop_spin( field_wrap );

			return false;

		}
	};

	/*
	 * Start and Stop Spin
	 */
	obj.start_spin = function ( field_wrap ) {
		/*var $div_height = $( form_wrap );
		 var $height = $div_height.height() + 'px';
		 $div_height.css( 'height', $height );*/
		$( field_wrap ).find( '#pngx-loading' ).show();
	};

	obj.stop_spin = function ( field_wrap ) {
		$( field_wrap ).find( '#pngx-loading' ).hide();
	};

	$( function () {
		obj.init();
	} );

})( jQuery, pngx_update_license );
