/**
 * Fields
 * @type {{}}
 */
var pngx_admin_fields_init = pngx_admin_fields_init || {};
(function ( $, obj ) {
	'use strict';

	obj.init = function () {
		this.init_scripts();
	};

	obj.init_scripts = function () {

		$( 'html' ).addClass( 'pngx-js' );

		obj.date_picker();

		obj.color_picker();

		/*
		* Hide Default Label
		*/
		$("tbody tr th:contains(Default)").css("display", "none");

		/*
		* Hide Row if Label is Empty
		*/
		$(".form-table label:empty").parent().hide();

	};

	/*
	 * WP Date Picker
	 */
	obj.date_picker = function ( helpid ) {

		$( '.pngx-datepicker' ).datepicker( {
			dateFormat: "mm/dd/yy",
			beforeShow: function ( input, inst ) {
				$( "#ui-datepicker-div" ).addClass( "pngx-ui" )
			}
		} );

	};

	/*
	 * Color Picker
	 */
	obj.color_picker = function ( helpid ) {

		$( '.pngx-color-picker' ).wpColorPicker();

	};

	/*
	 * Hide or Display Help Images
	 */
	obj.toggle__field_help = function ( helpid ) {

		var toggleImage = document.getElementById( helpid );

		if ( toggleImage.style.display == "inline" ) {
			document.getElementById( helpid ).style.display = 'none';
		} else {
			document.getElementById( helpid ).style.display = 'inline';
		}

		return false;
	};




	$( function () {
		obj.init();
	} );

})( jQuery, pngx_admin_fields_init );


/**
 * Media Upload Object
 * @type {{}}
 */
function PNGX__Media( $, field_id, upload_title, button_text ) {

	this.field_id = field_id;
	this.upload_title = upload_title;
	this.button_text = button_text;

	this.init = function () {
		this.upload();
		this.clear();
	};


	this.upload = function () {

		var upload_title = this.upload_title;
		var button_text = this.button_text;
		var field_id = this.field_id;

		/*
		 * Media Manager 3.5
		 */
		$( '.pngx-image-button' ).click( function ( e ) {

			//Create Media Manager On Click to allow multiple on one Page
			var img_uploader, attachment;

			e.preventDefault();

			//Setup the Variables based on the Button Clicked to enable multiple
			var img_input_id = '#' + this.id + '.pngx-upload-image';
			var img_src = 'img#' + this.id + '.pngx-image';
			var default_msg = 'div#' + this.id + '.pngx-default-image';

			//If the uploader object has already been created, reopen the dialog
			if ( img_uploader ) {
				img_uploader.open();
				return;
			}

			//Extend the wp.media object
			img_uploader = wp.media.frames.file_frame = wp.media( {
				title: upload_title,
				button: {
					text: button_text
				},
				multiple: false
			} );

			//When a file is selected, grab the URL and set it as the text field's value
			img_uploader.on( 'select', function () {
				attachment = img_uploader.state().get( 'selection' ).first().toJSON();
				//Set the Field with the Image ID
				$( img_input_id ).val( attachment.id );
				//Set the Sample Image with the URL
				$( img_src ).attr( 'src', attachment.url );
				//Show Image
				$( img_src ).show();
				//Hide Message
				$( default_msg ).hide();
				//Trigger New Image Uploaded
				$( field_id ).trigger( 'display' );
			} );

			//Open the uploader dialog
			img_uploader.open();

		} );

	};

	this.clear = function () {

		var field_id = this.field_id;
		/*
		 * Remove Image and replace with default and Erase Image ID for Coupon
		 */
		$( '.pngx-clear-image' ).click( function ( e ) {
			e.preventDefault();
			var remove_input_id = 'input#' + this.id + '.pngx-upload-image';
			var img_src = 'img#' + this.id + '.pngx-image';

			$( remove_input_id ).val( '' );
			$( img_src ).hide();
			$( 'div#' + this.id + '.pngx-default-image' ).show();
			$( field_id ).trigger( 'display' );
		} );

	};


	this.init()

}

/**
 * Help
 * @type {{}}
 */
var pngx_admin_help_scripts = pngx_admin_help_scripts || {};
(function ( $, obj ) {
	'use strict';

	obj.init = function () {
		this.init_scripts();
	};

	obj.init_scripts = function () {

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

		/*
		 * Color Box Init for Help Videos
		 */
		$( ".youtube_colorbox" ).colorbox( {
			rel: "how_to_videos",
			current: "video {current} of {total}",
			iframe: true,
			width: "90%",
			height: "90%"
		} );

	};

	$( function () {
		obj.init();
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
	obj.data = obj.tabs = obj.updated_tab = obj.page_id = '';
	obj.tab_text = obj.tab_total_length = 0;
	obj.tab_count = 1;

	obj.init = function ( wrap ) {
		if ( wrap ) {
			obj.tab_wrap = wrap;
		}

		//Set Tabs
		obj.data =  $( obj.tab_wrap ).data();
		obj.tabs = obj.data.toggleTabs;
		obj.updated_tab = obj.data.toggleUpdate_message;
		obj.id = obj.data.toggleId;

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

		var wrapped = $( '.wrap h2' ).wrap( '<div class="' + obj.tab_wrap.replace( /\./g, ' ' ) + '-panel">' );

		wrapped.each( function () {
			$( this ).parent().append( $( this ).parent().nextUntil( 'div' + obj.tab_wrap + '-panel' ) );
		} );

		$( obj.tab_wrap + '-panel' ).each( function ( index ) {
			$( this ).attr( 'id', obj.tabs[$( this ).children( 'h2' ).text()] );
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


/**
 * Fields Toggle
 * @type {{}}
 */
var pngx_fields_toggle = pngx_fields_toggle || {};
(function ( $, obj ) {
	'use strict';

	obj.tab_wrap = '.pngx-tabs';
	obj.tabs = '';

	obj.init = function ( field_check, remove_img ) {

		obj.prepare( field_check, remove_img );

	};

	obj.toggle = function ( field, field_group, field_display, message ) {

		if ( !field || !field_group ) {
			return;
		}

		var display = obj.toggle_field_manager( field );
		if ( 'hide' === display || 'toggle' === display ) {
			$( field_group ).each( function () {
				$( this ).fadeOut();
			} );

			if ( field_display ) {
				$( field_display ).each( function () {
					$( this ).fadeIn( 'fast' );
				} );
			}
		} else {
			$( field_group ).each( function () {
				$( this ).fadeIn( 'fast' );
			} );
		}

		//Only Run if Variable is an Object
		if ( 'object' === typeof message ) {
			obj.toggle_msg( message, display );
		}

	};

	obj.toggle_field_manager = function ( field ) {

		var $field_type = $( field ).prop( 'nodeName' );

		if ( 'INPUT' == $field_type ) {
			if ( $( field ).val() ) {
				return 'hide';
			} else {
				return 'show';
			}
		} else if ( 'SELECT' == $field_type ) {
			return 'toggle';
		} else if ( 'CHECK' == $field_type ) {

		}

	};

	obj.toggle_msg = function ( message, display ) {
		//Remove Message
		$.each( message, function ( key, value ) {
			var div_class = '.pngx-tab-heading-' + key;
			$( div_class ).next( 'div.pngx-error' ).remove();
		} );
		//Add Message
		if ( 'hide' === display ) {
			$.each( message, function ( key, value ) {
				if ( key && value ) {
					var div_class = '.pngx-tab-heading-' + key;
					$( div_class ).after( '<div class="pngx-error">' + value + '</div>' );
				}
			} );
		}
	};

})( jQuery, pngx_fields_toggle );