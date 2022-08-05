var pngx = pngx || {};
pngx.tabs = pngx.tabs || {};

(function ( obj ) {
	'use strict';

	document.addEventListener(
		'DOMContentLoaded',
		function () {
			pngx.tabs.forEach( function ( tabs ) {
				var objName = 'tabs_obj_' + tabs.id;
				window[objName] = new window.A11yTabInterface( {
					id: tabs.id,
					tabbed: tabs.id,
					tablist: tabs.tablist,
					tabs: tabs.tabs,
					initialTab: tabs.initialTab,
				} );
			} );
		}
	)
})( pngx.tabs );
