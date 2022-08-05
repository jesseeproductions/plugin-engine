/**
 * Ally Tabbed Interface
 * based off https://inclusive-components.design/tabbed-interfaces/
 *
 * @since 1.0
 *
 */
(function ( global ) {

	/**
	 * Define the constructor to instantiate a tabbed inteface
	 *
	 * @constructor
	 * @param {Object} options
	 */
	function A11yTabInterface( options ) {
		this.options = extend( {
			id: '',
			tabbed: '.tabbed',
			tablist: 'ul',
			tabs: 'a',
			initialTab: 0,
		}, options );

		this.options.panels = '[id^="' + this.options.id + '"]';

		// Get relevant elements and collections
		this.tabbed = document.getElementById( this.options.tabbed );
		this.tablist = this.tabbed.querySelector( this.options.tablist );
		this.tabs = this.tablist.querySelectorAll( this.options.tabs );
		this.panels = this.tabbed.querySelectorAll( this.options.panels );

		// Add the tablist role to the first <ul> in the .tabbed container
		this.tablist.setAttribute( 'role', 'tablist' );

		this.create();

		// Initially activate the first tab and reveal the first tab panel
		this.tabs[this.options.initialTab].removeAttribute( 'tabindex' );
		this.tabs[this.options.initialTab].setAttribute( 'aria-selected', 'true' );
		this.panels[this.options.initialTab].hidden = false;
	}

	// The tab switching function
	A11yTabInterface.prototype.switchTab = function ( oldTab, newTab ) {
		newTab.focus();
		// Make the active tab focusable by the user (Tab key)
		newTab.removeAttribute( 'tabindex' );
		// Set the selected state
		newTab.setAttribute( 'aria-selected', 'true' );
		oldTab.removeAttribute( 'aria-selected' );
		oldTab.setAttribute( 'tabindex', '-1' );
		// Get the indices of the new and old tabs to find the correct
		// tab panels to show and hide
		let index = Array.prototype.indexOf.call( this.tabs, newTab );
		let oldIndex = Array.prototype.indexOf.call( this.tabs, oldTab );
		this.panels[oldIndex].hidden = true;
		this.panels[index].hidden = false;
	}

	// Add semantics are remove user focusability for each tab
	A11yTabInterface.prototype.create = function () {

		Array.prototype.forEach.call( this.tabs, ( tab, i ) => {
			tab.setAttribute( 'role', 'tab' );
			tab.setAttribute( 'id', this.options.id + '-' +(i + 1) );
			tab.setAttribute( 'tabindex', '-1' );
			tab.parentNode.setAttribute( 'role', 'presentation' );

			// Handle clicking of tabs for mouse users
			tab.addEventListener( 'click', e => {
				e.preventDefault();
				let currentTab = this.tablist.querySelector( '[aria-selected]' );
				if ( e.currentTarget !== currentTab ) {
					this.switchTab( currentTab, e.currentTarget );
				}
			} );

			// Handle keydown events for keyboard users
			tab.addEventListener( 'keydown', e => {
				// Get the index of the current tab in the tabs node list
				let index = Array.prototype.indexOf.call( this.tabs, e.currentTarget );
				// Work out which key the user is pressing and
				// Calculate the new tab's index where appropriate
				let dir = e.which === 37 ? index - 1 : e.which === 39 ? index + 1 : e.which === 40 ? 'down' : null;
				if ( dir !== null ) {
					e.preventDefault();
					// If the down key is pressed, move focus to the open panel,
					// otherwise switch to the adjacent tab
					dir === 'down' ? this.panels[i].focus() : this.tabs[dir] ? this.switchTab( e.currentTarget, this.tabs[dir] ) : void 0;
				}
			} );
		} );


		// Add tab panel semantics and hide them all
		Array.prototype.forEach.call( this.panels, ( panel, i ) => {
			panel.setAttribute( 'role', 'tabpanel' );
			panel.setAttribute( 'tabindex', '-1' );
			panel.setAttribute( 'aria-labelledby', this.tabs[i].id );
			panel.hidden = true;
		} );

	};

	function extend( obj, src ) {
		Object.keys( src ).forEach( function ( key ) {
			obj[key] = src[key];
		} );
		return obj;
	}

	if ( typeof module !== 'undefined' && typeof module.exports !== 'undefined' ) {
		module.exports = A11yTabInterface;
	} else if ( typeof define === 'function' && define.amd ) {
		define( 'A11yTabInterface', [], function () {
			return A11yTabInterface;
		} );
	} else if ( typeof global === 'object' ) {
		global.A11yTabInterface = A11yTabInterface;
	}
}( typeof global !== 'undefined' ? global : window ));