/**
 * Help Section Scripts
 * @type {{}}
 */
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


console.log('define variable');
/*
 * jQuery UI Tabs for Meta Box
 *
 */
var Pngx__Tabs = {

	init: function ( sections ) {
		console.log(sections);

		var sections = sections;
		init_scripts();
	},

	init_scripts: function () {

		var wrapped = $( ".wrap h3" ).wrap( "<div class=\"pngx-tabs-panel\">" );

		wrapped.each( function () {
			$( this ).parent().append( $( this ).parent().nextUntil( "div.pngx-tabs-panel" ) );
		} );

		$( ".pngx-tabs-panel" ).each( function ( index ) {
			$( this ).attr( "id", sections[$( this ).children( "h3" ).text()] );
			if ( index > 0 )
				$( this ).addClass( "pngx-tabs-hide" );
		} );


		$( function () {
			//  http://stackoverflow.com/questions/4299435/remember-which-tab-was-active-after-refresh
			//	jQueryUI 1.10 and HTML5 ready
			//      http://jqueryui.com/upgrade-guide/1.10/#removed-cookie-option
			//  Documentation
			//      http://api.jqueryui.com/tabs/#option-active
			//      http://api.jqueryui.com/tabs/#event-activate
			//      http://balaarjunan.wordpress.com/2010/11/10/html5-session-storage-key-things-to-consider/
			//

			//  Define friendly index name
			var index = "pngx-meta-tab" + cctor_coupon_meta_js_vars.cctor_coupon_id;

			//  Define friendly data store name
			var dataStore = window.sessionStorage;

			//If Saved then use tab index, otherwise default to first tab
			if ( cctor_coupon_meta_js_vars.cctor_coupon_updated ) {
				//  Start magic!
				try {
					// getter: Fetch previous value
					var oldIndex = dataStore.getItem( index );

				} catch ( e ) {
					// getter: Always default to first tab in error state
					var oldIndex = 0;
				}
			} else {

				var oldIndex = 0;

			}

			// Tab initialization
			$( ".pngx-tabs" ).tabs( {

				// The zero-based index of the panel that is active (open)
				active: oldIndex,
				// Triggered after a tab has been activated
				activate: function ( event, ui ) {

					//  Get future value
					var newIndex = ui.newTab.parent().children().index( ui.newTab );
					//  Set future value
					dataStore.setItem( index, newIndex )

					//Set Responsive Menu Text to Current Tab
					var selectedTab = $( '.pngx-tabs' ).tabs( 'option', 'active' );
					$( '.pngx-tabs-nav-mobile' ).text( $( '.pngx-tabs ul li a' ).eq( selectedTab ).text() );
				},
				fx: {opacity: "toggle", duration: "fast"},

			} );
		} );

		$( "input[type=text], textarea" ).each( function () {
			if ( $( this ).val() == $( this ).attr( "placeholder" ) || $( this ).val() == "" )
				$( this ).css( "color", "#999" );
		} );

		$( "input[type=text], textarea" ).focus( function () {
			if ( $( this ).val() == $( this ).attr( "placeholder" ) || $( this ).val() == "" ) {
				$( this ).val( "" );
				$( this ).css( "color", "#000" );
			}
		} ).blur( function () {
			if ( $( this ).val() == "" || $( this ).val() == $( this ).attr( "placeholder" ) ) {
				$( this ).val( $( this ).attr( "placeholder" ) );
				$( this ).css( "color", "#999" );
			}
		} );

		$( ".wrap h3, .wrap table" ).show();

		// Browser compatibility
		if ( $.browser.mozilla )
			$( "form" ).attr( "autocomplete", "off" );

		/*
		 * 	Responsive Tabs Find Breakpoint to Change or Accordion Layout or Back to Tabs
		 * 	since 2.0
		 */
		//Calculate Total Tab Length to determine when to switch between Responsive and Regular Tabs
		var tabText = 0;
		var tabCount = 0;

		$( ".pngx-tabs-nav li" ).each( function () {

			tabText = tabText + $( this ).my.textWidth();

			tabCount = tabCount + 1;

		} );

		//On Resize or Load check if Tabs will fit
		$( window ).on( 'resize load', function ( e ) {

			// 38px per tab for padding
			var tabTotallength = tabText + ( tabCount * 38 );

			if ( tabTotallength > $( '.pngx-tabs' ).width() ) {

				$( '.pngx-tabs-nav' ).addClass( 'pngx-accordian-tabs' );
				$( '.pngx-tabs-nav-mobile' ).addClass( 'show' );

			} else {

				$( '.pngx-tabs-nav' ).fadeIn( 'fast', function () {
					$( this ).removeClass( 'pngx-accordian-tabs' );
				} );
				$( '.pngx-tabs-nav-mobile' ).removeClass( 'show' );
			}
		} );


		/*
		 * Tabs Responsive Mode
		 *
		 * since 2.0
		 */
		$( '.pngx-tabs-nav' ).before( '<div class="pngx-tabs-nav-mobile">Menu</div>' );

		//Change Menu Text on Creation of Tabs
		$( ".pngx-tabs" ).on( "tabscreate", function ( event, ui ) {

			var selectedTab = $( '.pngx-tabs' ).tabs( 'option', 'active' );

			$( '.pngx-tabs-nav-mobile' ).text( $( '.pngx-tabs ul li a' ).eq( selectedTab ).text() );

		} );

		//Open Tabs in Responsive Mode
		$( document ).on( 'click', '.pngx-tabs-nav-mobile', function ( event ) {

			var tabClass = $( this ).attr( 'class' ).split( " " )[0];

			my.toggleMobileMenu( event, tabClass );
		} )

	},

	/*
	 *	Calculate Width of Text
	 *	http://stackoverflow.com/questions/1582534/calculating-text-width-with-jquery
	 */
	textWidth: function ( text, font ) {
		if ( !$.fn.textWidth.fakeEl ) $.fn.textWidth.fakeEl = $( '<span>' ).hide().appendTo( document.body );
		$.fn.textWidth.fakeEl.text( text || this.val() || this.text() ).css( 'font', font || this.css( 'font' ) );
		return $.fn.textWidth.fakeEl.width();
	},

	/*
	 * Toogle Responsive Tabs
	 */
	toggleMobileMenu: function ( event, tabClass ) {
		tabClass = tabClass.slice( 0, -7 );

		$( '.' + tabClass ).slideToggle();
	}

}



//new Pngx__Tabs.init( {"Content":"content"} );