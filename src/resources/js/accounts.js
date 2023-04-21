/* eslint-disable template-curly-spacing */
/**
 * Makes sure we have all the required levels on the pngx Object
 *
 * @since 0.1.0
 *
 * @type {PlainObject}
 */
pngx.accounts = pngx.accounts || {};

(function( $, obj, pngxLoader, pngxDropdowns, pngxAccordion ) {
	'use-strict';
	const $document = $( document );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 0.1.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		accountContainer: '.pngx-wrapper',
		accountAdd: '.pngx-engine-options__add-account-button',
		messageWrap: '.pngx-engine-options-message__wrap',
		formTable: '.form-table',
		defaultAccount: '.pngx-engine-default-account-select-field',

		// Individual Accounts.
		accountList: '.pngx-engine-options-items__wrap',
		accountItem: '.pngx-engine-grid-row',
		accountName: '.pngx-engine-options-details__name-input',
		accountApiKey: '.pngx-engine-options-details__api-key-input',
		accountDefaults: '.pngx-engine-options-account-defaults__input',
		accountSave: '.pngx-engine-options-details-action__save',
		accountUpdate: '.pngx-engine-options-details-action__update',
		accountDelete: '.pngx-engine-options-details-action__delete',

		// Loader
		pngxAdminLoader: '.pngx-admin-loader',
	};

	/**
	 * Scroll to bottom of list of Accounts.
	 *
	 * @since 0.1.0
	 *
	 * @param {jQuery} $container The jQuery object of the account's setting container.
	 */
	obj.scrollToBottom = function( $container ) {
		let totalHeight = 0;
		$container.find( obj.selectors.accountItem ).each( function() {
			totalHeight += $( this ).outerHeight();
		} );

		$( obj.selectors.accountList ).animate( {
			scrollTop: totalHeight
		}, 500 );
	};

	/**
	 * Validates the description and users field is setup for the key pair.
	 *
	 * @since 0.1.0
	 *
	 * @param {jQuery} $accountItem The jQuery object of the account item wrap.
	 *
	 * @returns {boolean} Whether the description and user field has values.
	 */
	obj.validateFields = function( $accountItem ) {
		const accountName = $accountItem.find( obj.selectors.accountName ).val();
		const intergrationApiKey = $accountItem.find( obj.selectors.accountApiKey ).val();
		if ( accountName && intergrationApiKey ) {
			return true;
		}

		return false;
	};

	/**
	 * Sends an ajax request and calls the onSuccess callback on successful response
	 *
	 * @since 0.1.0
	 *
	 * @param {string} url The URL for the AJAX request
	 * @param {object} requestData The data to send in the AJAX request
	 * @param {Function} onSuccess The callback function to call on successful response
	 * @param {object} event_target The target element of the event
	 */
	const sendAjaxRequest = ( url, requestData, onSuccess, event_target ) => {
		const $accountContainer = $( event_target ).closest( obj.selectors.accountContainer );
		pngxLoader.show( $accountContainer, obj.selectors.pngxAdminLoader );

		$.ajax( {
			url,
			contentType: 'application/json',
			context: $( obj.selectors.accountList ),
			data: requestData,
			success: ( html ) => onSuccess( html, event_target ),
		} );
	};

	/**
	 * Handles the successful response from the backend to add Account fields.
	 *
	 * @since 0.1.0
	 *
	 * @param {string} html The HTML that adds a message and the page fields html.
	 * @param {object} event_target The target element of the event.
	 */
	obj.onAddAccountFieldsSuccess = function( html, event_target ) {
		const $this = $( event_target );
		const $table = $this.closest( obj.selectors.formTable );
		const $container = $this.closest( obj.selectors.accountContainer );
		const message = $( html ).filter( obj.selectors.messageWrap );
		const accountItemWrap = $( html ).filter( obj.selectors.accountItem );

		$table.find( obj.selectors.messageWrap ).html( message );

		pngxLoader.hide( $container, obj.selectors.pngxAdminLoader );

		if ( accountItemWrap.length > 0 ) {
			$table.find( obj.selectors.accountList ).append( accountItemWrap );

			// Setup dropdowns after loading account fields.
			const $dropdowns = $( obj.selectors.accountList )
				.find( pngxDropdowns.selector.dropdown )
				.not( pngxDropdowns.selector.created );

			$dropdowns.pngx_dropdowns();

			pngxAccordion.bindAccordionEvents( accountItemWrap );
		}
	};


	/**
	 * Handles adding a new account fields.
	 *
	 * @since 0.1.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleAddAccount = ( event ) => {
		event.preventDefault();
		const url = $( event.target ).attr( 'href' );
		sendAjaxRequest( url, {}, obj.onAddAccountFieldsSuccess, event.target );
	};

	/**
	 * Handles setting up the default account dropdown on save or delete of an account.
	 *
	 * @since 0.1.
	 *
	 * @param $table    The jQuery object of the table.
	 * @param accountDefaultAccounts    The HTML of the default account dropdown.
	 */
	obj.handleDefaultAccounts = function( $table, accountDefaultAccounts ) {
		if ( 0 === accountDefaultAccounts.length ) {
			return
		}

		$table.find( obj.selectors.defaultAccount ).html( accountDefaultAccounts );

		// Setup dropdowns after loading account fields.
		const $dropdowns = $( obj.selectors.defaultAccount )
			.find( pngxDropdowns.selector.dropdown )
			.not( pngxDropdowns.selector.created );

		$dropdowns.pngx_dropdowns();
	};

	/**
	 * Replaces the account item in the table.
	 *
	 * @since 0.1.0
	 *
	 * @param {object} $table The table jQuery object.
	 * @param {object} $accountItemWrap The account item wrap jQuery object.
	 */
	obj.replaceAccountItem = function( $table, $accountItemWrap ) {
		if ( 0 === $accountItemWrap.length ) {
			return;
		}

		const uniqueId = $accountItemWrap.data( 'uniqueId' );
		const existingPage = $table.find( `[data-unique-id='${uniqueId}']` );
		existingPage.replaceWith( $accountItemWrap );

		pngxAccordion.bindAccordionEvents( $accountItemWrap );
	};

	/**
	 * Handles the successful response from the backend to save or update accounts.
	 *
	 * @since 0.1.0
	 *
	 * @param {string} html The HTML that adds a message and the page fields html.
	 * @param {object} event_target The target element of the event.
	 */
	obj.onAccountSuccess = function( html, event_target ) {
		const $this = $( event_target );
		const $table = $this.closest( obj.selectors.formTable );
		const $container = $this.closest( obj.selectors.accountContainer );
		const $message = $( html ).filter( obj.selectors.messageWrap );
		const $accountItemWrap = $( html ).filter( obj.selectors.accountItem );
		const accountDefaultAccounts = $( html ).filter( obj.selectors.defaultAccount );

		$table.find( obj.selectors.messageWrap ).html( $message );
		pngxLoader.hide( $container, obj.selectors.pngxAdminLoader );

		obj.replaceAccountItem( $table, $accountItemWrap );
		obj.handleDefaultAccounts( $table, accountDefaultAccounts );
	};

	/**
	 * Handles generating consumer id and secret.
	 *
	 * @since 0.1.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleSave = function( event ) {
		event.preventDefault();

		const $this = $( event.target ).closest( obj.selectors.accountSave );
		const url = $this.data( 'ajaxSaveUrl' );
		const $accountItem = $this.closest( obj.selectors.accountItem );
		const is_valid = obj.validateFields( $accountItem );
		if ( !is_valid ) {
			window.alert( $this.data( 'generateError' ) );
			return;
		}

		const uniqueId = $accountItem.data( 'uniqueId' );
		const accountName = $accountItem.find( obj.selectors.accountName ).val();
		const intergrationApiKey = $accountItem.find( obj.selectors.accountApiKey ).val();
		const intergrationDefaults = obj.getInputValuesAndNames( obj.selectors.accountDefaults, $accountItem );

		sendAjaxRequest( url, {
			unique_id: uniqueId,
			api_key: intergrationApiKey,
			name: accountName,
			defaults: intergrationDefaults,
		}, obj.onAccountSuccess, $this );
	};

	/**
	 * Handles generating consumer id and secret.
	 *
	 * @since 0.1.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleUpdate = function( event ) {
		event.preventDefault();

		const $this = $( event.target ).closest( obj.selectors.accountUpdate );
		const url = $this.data( 'ajaxUpdateUrl' );
		const $accountItem = $this.closest( obj.selectors.accountItem );
		const uniqueId = $accountItem.data( 'uniqueId' );
		const intergrationDefaults = obj.getInputValuesAndNames( obj.selectors.accountDefaults, $accountItem );

		sendAjaxRequest( url, {
			unique_id: uniqueId,
			defaults: intergrationDefaults,
		}, obj.onAccountSuccess, $this );
	};

	/**
	 * Handles the successful response from the backend to delete an account.
	 *
	 * @since 0.1.0
	 *
	 * @param {string} html The HTML that adds a message and the page fields html.
	 * @param {object} event_target The target element of the event.
	 */
	obj.onDeleteSuccess = function( html, event_target ) {
		const $this = $( event_target );
		const $message = $( html ).filter( obj.selectors.messageWrap );
		const $accountContainer = $( obj.selectors.accountContainer );
		const $table = $this.closest( obj.selectors.formTable );
		const accountDefaultAccounts = $( html ).filter( obj.selectors.defaultAccount );

		$table.find( obj.selectors.messageWrap ).html( $message );

		pngxLoader.hide( $accountContainer, obj.selectors.pngxAdminLoader );

		// Delete marked account wrap.
		$( `${obj.selectors.accountItem}.to-delete` ).remove();

		obj.handleDefaultAccounts( $table, accountDefaultAccounts );
	};

	/**
	 * Handles revoking an account key.
	 *
	 * @since 0.1.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleDelete = function( event ) {
		event.preventDefault();

		const $this = $( event.target ).closest( obj.selectors.accountDelete );
		const url = $this.data( 'ajaxDeleteUrl' );
		const $accountItem = $this.closest( obj.selectors.accountItem );
		const uniqueId = $accountItem.data( 'uniqueId' );
		const confirmed = confirm( $this.data( 'confirmation' ) );
		if ( !confirmed ) {
			return;
		}

		// Add a class to mark for deletion.
		$accountItem.addClass( 'to-delete' );

		sendAjaxRequest( url, {
			unique_id: uniqueId,
		}, obj.onDeleteSuccess, $this );
	};

	/**
	 * Get input values and names for elements with the specified class in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param {string} className The class name to search for in the container.
	 * @param {jQuery} $container The jQuery container in which to search for the class.
	 *
	 * @return {Object} An object with input names as keys and their values as values.
	 */
	obj.getInputValuesAndNames = function( className, $container ) {
		const inputs = $container.find( className ).filter( function() {
			const $element = $( this );
			return $element.is( 'input, select, textarea' ) &&
				$element.prop( 'type' ) !== 'button' &&
				$element.prop( 'type' ) !== 'submit';
		} );

		const results = Array.from( inputs ).reduce( function( acc, input ) {
			const $input = $( input );
			const fullName = $input.prop( 'name' );
			const nameMatch = fullName.match( /\['defaults'\]\['([^']*)'\]/ );

			if ( nameMatch && nameMatch.length > 1 ) {
				const name = nameMatch[1];
				acc[name] = $input.val();
			}

			return acc;
		}, {} );

		return results;
	};

	/**
	 * Bind the account events.
	 *
	 * @since 0.1.0
	 */
	obj.bindEvents = function() {
		$document
			.on( 'click', obj.selectors.accountUpdate, obj.handleUpdate )
			.on( 'click', obj.selectors.accountSave, obj.handleSave )
			.on( 'click', obj.selectors.accountDelete, obj.handleDelete )
			.on( 'click', obj.selectors.accountAdd, obj.handleAddAccount );

		pngxAccordion.bindAccordionEvents( $( obj.selectors.accountContainer ) );
	};

	/**
	 * Unbind the account events.
	 *
	 * @since 0.1.0
	 */
	obj.unbindEvents = function() {
	};

	/**
	 * Handles the initialization of the admin when Document is ready
	 *
	 * @since 0.1.0
	 */
	obj.ready = function() {
		obj.bindEvents();
	};

	// Configure on document ready
	$( obj.ready );
})( jQuery, pngx.accounts, pngx.loader, pngx.dropdowns, pngx.pngxAccordion );
