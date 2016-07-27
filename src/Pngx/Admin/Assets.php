<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Register Admin Assets for Plugin Engine
 *
 *
 */
class Pngx__Admin__Assets {

	/*
	* Register Assets
	*/
	public static function register_assets() {

		// @formatter:off
		wp_register_style(
			'pngx-admin',
			Pngx__Main::instance()->resource_url . 'css/pngx-admin.css',
			false,
			filemtime( Pngx__Main::instance()->resource_path . 'css/pngx-admin.css' )
		);

		wp_register_script(
			'pngx-admin',
			Pngx__Main::instance()->resource_url . 'js/pngx-admin.js',
			array( 'jquery-ui-tabs' ),
			filemtime( Pngx__Main::instance()->resource_path . 'js/pngx-admin.js' ),
			true
		);
		// @formatter:on

		/**
		 * Hook to Register New Scripts or Styles for the Admin
		 */
		do_action( 'pngx_admin_scripts_styles' );

	}

}