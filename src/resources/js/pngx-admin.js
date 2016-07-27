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

/**
 * jQuery UI Tabs
 * @type {{}}
 */
var pngx_admin_tabs = pngx_admin_tabs || {};
(function ( $, obj ) {
	'use strict';

	obj.tab_wrap = '.pngx-tabs';
	obj.tabs = '';
	obj.updated_tab = '';
	obj.page_id = '';
	obj.tab_text = 0;
	obj.tab_count = 1;
	obj.tab_total_length = 0;

	obj.init = function ( wrap, sections, updated_tab, page_id ) {
		if ( wrap ) {
			obj.tab_wrap = wrap;
		}
		obj.tabs = sections;
		obj.updated_tab = updated_tab;
		obj.page_id = page_id;

		obj.init_tabs();

	};

	obj.init_tabs = function () {

		obj.wrap_tab_areas();

		obj.setup_tabs();

		obj.tab_breakpoint();

		obj.setup_responsive_accordion();

		//On Resize or Load check if Tabs will fit
		$( window ).on( 'resize load', function ( e ) {
			// 40px per tab for padding
			obj.tab_total_length = obj.tab_text + ( obj.tab_count * 40 );
			if ( obj.tab_total_length > $( obj.tab_wrap ).width() ) {
				$( obj.tab_wrap + '-nav' ).addClass( obj.tab_wrap.replace( /\./g, ' ' ) + '-accordian' );
				$( obj.tab_wrap + '-nav-mobile' ).addClass( 'show' );
			} else {
				$( obj.tab_wrap + '-nav' ).fadeIn( 'fast', function () {
					$( this ).removeClass( obj.tab_wrap.replace( /\./g, ' ' ) + '-accordian' );
				} );
				$( obj.tab_wrap + '-nav-mobile' ).removeClass( 'show' );
			}
		} );

		//Open Tabs in Responsive Mode
		$( document ).on( 'click', obj.tab_wrap + '-nav-mobile', function ( event ) {
			var tabClass = $( this ).attr( 'class' ).split( " " )[0];
			toggle_mobile_menu( event, tabClass );
		} )
	};

	/*
	 * Wrap Tab Areas
	 */
	obj.wrap_tab_areas = function () {

		var wrapped = $( '.wrap h3' ).wrap( '<div class="' + obj.tab_wrap.replace( /\./g, ' ' ) + '-panel">' );

		wrapped.each( function () {
			$( this ).parent().append( $( this ).parent().nextUntil( 'div' + obj.tab_wrap + '-panel' ) );
		} );

		$( obj.tab_wrap + '-panel' ).each( function ( index ) {
			$( this ).attr( 'id', obj.tabs[$( this ).children( 'h3' ).text()] );
			if ( index > 0 )
				$( this ).addClass( obj.tab_wrap + '-hide' );
		} );

	};

	/*
	 * Init Tabs
	 */
	obj.setup_tabs = function () {
		/*
		 *	Coding Built from the following resources
		 *  http://stackoverflow.com/questions/4299435/remember-which-tab-was-active-after-refresh
		 *  http://jqueryui.com/upgrade-guide/1.10/#removed-cookie-option
		 *  http://api.jqueryui.com/tabs/#option-active
		 *  http://api.jqueryui.com/tabs/#event-activate
		 *  http://balaarjunan.wordpress.com/2010/11/10/html5-session-storage-key-things-to-consider/
		 */

		//  Define friendly index name
		var index = obj.tab_wrap + '-index-' + obj.id;

		//  Define friendly data store name
		var data_store = window.sessionStorage;

		//old index value
		var old_index = '';

		//If Saved then use tab index, otherwise default to first tab
		if ( obj.updated_tab ) {
			try {
				// getter: Fetch previous value
				old_index = data_store.getItem( index );
			} catch ( e ) {
				// getter: Always default to first tab in error state
				old_index = 0;
			}
		} else {
			old_index = 0;
		}

		// Tab initialization
		$( obj.tab_wrap ).tabs( {
			// The zero-based index of the panel that is active (open)
			active: old_index,
			// Triggered after a tab has been activated
			activate: function ( event, ui ) {
				//  Get new value
				var new_index = ui.newTab.parent().children().index( ui.newTab );
				//  Set future value
				data_store.setItem( index, new_index );

				//Set Responsive Menu Text to Current Tab
				var selectedTab = $( obj.tab_wrap ).tabs( 'option', 'active' );
				$( obj.tab_wrap + '-nav-mobile' ).text( $( obj.tab_wrap + ' ul li a' ).eq( selectedTab ).text() );
			},
			fx: {opacity: "toggle", duration: "fast"}
		} );

		$( obj.tab_wrap + ' h3, ' + obj.tab_wrap + 'table' ).show();

		if ( $.browser.mozilla ) {
			$( "form" ).attr( "autocomplete", "off" );
		}
	};

	/*
	 * Responsive Tab Breakpoint
	 */
	obj.tab_breakpoint = function () {

		$( obj.tab_wrap + '-nav li' ).each( function () {

			obj.tab_text = obj.tab_text + $( this ).find( 'a' ).width();

			obj.tab_count = obj.tab_count + 1;

		} );

	};

	/*
	 * Setup Responsive Accordion
	 */
	obj.setup_responsive_accordion = function () {

		$( obj.tab_wrap + '-nav' ).before( '<div class="' + obj.tab_wrap.replace( /\./g, ' ' ) + '-nav-mobile">Menu</div>' );

		//Change Menu Text on Creation of Tabs
		$( obj.tab_wrap ).on( 'tabscreate', function ( event, ui ) {
			var selectedTab = $( obj.tab_wrap ).tabs( 'option', 'active' );
			$( obj.tab_wrap + '-nav-mobile' ).text( $( obj.tab_wrap + ' ul li a' ).eq( selectedTab ).text() );
		} );

	};

	/*
	 * Toogle Responsive Tabs
	 */
	function toggle_mobile_menu( event, tabClass ) {
		tabClass = tabClass.slice( 0, -7 );

		$( '.' + tabClass ).slideToggle();
	}

})( jQuery, pngx_admin_tabs );

var $ = jQuery.noConflict();

jQuery( function ( $ ) {
	/*$( "input[type=text], textarea" ).each( function () {
	 console.log( 'here1' );
	 if ( $( this ).val() == $( this ).attr( "placeholder" ) || $( this ).val() == "" )
	 $( this ).css( "color", "#999" );
	 } );

	 $( "input[type=text], textarea" ).focus( function () {
	 console.log( 'here2' );
	 if ( $( this ).val() == $( this ).attr( "placeholder" ) || $( this ).val() == "" ) {
	 $( this ).val( "" );
	 $( this ).css( "color", "#000" );
	 }
	 } ).blur( function () {
	 console.log( 'here3' );
	 if ( $( this ).val() == "" || $( this ).val() == $( this ).attr( "placeholder" ) ) {
	 $( this ).val( $( this ).attr( "placeholder" ) );
	 $( this ).css( "color", "#999" );
	 }
	 } );*/
} );