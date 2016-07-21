var pngx_admin_help_scripts = pngx_admin_help_scripts || {};

(function ( $, my ) {
	'use strict';

	my.init = function () {
		this.init_scripts();
	};

	my.init_scripts = function () {

		/*
		 * Help Slideout
		 */
		$( ".pngx-section-help-container-toggle" ).on( "click", function ( event ) {

			event.preventDefault();
			var $help_wrap = $( this ).parent();
			var $help_section = $help_wrap.find( '.pngx-section-help-slideout' );

			$help_section.animate( {
				height: "toggle",
				opacity: "toggle"
			}, "fast" );

		} );

	};

	$( function () {
		my.init();
	} );

})( jQuery, pngx_admin_help_scripts );