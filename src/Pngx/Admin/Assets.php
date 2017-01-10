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
			'pngx-color-picker-alpha',
			Pngx__Main::instance()->vendor_url . 'wp-color-picker-alpha/wp-color-picker-alpha.min.js',
			array(),
			filemtime( Pngx__Main::instance()->vendor_path . 'wp-color-picker-alpha/wp-color-picker-alpha.min.js' ),
			true
		);

		wp_register_script(
			'pngx-admin',
			Pngx__Main::instance()->resource_url . 'js/pngx-admin.js',
			array( 'pngx-color-picker-alpha', 'jquery-ui-tabs' ),
			filemtime( Pngx__Main::instance()->resource_path . 'js/pngx-admin.js' ),
			true
		);
		// @formatter:on

		/**
		 * Hook to Register New Scripts or Styles for the Admin
		 */
		do_action( 'pngx_admin_scripts_styles' );

	}

	/*
	* Enqueue Plugin Engine Assets
	*/
	public static function load_assets( ) {

	}

	/*
	* Detect if External Asset is Available
	*/
	public static function detect_external_asset( $file ) {

		$file_headers = @get_headers( $file );
		if ( FALSE === $file_headers || 'HTTP/1.0 404 Not Found' == $file_headers[0] ) {
			return false;
		}

			return true;

	}

}