<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Plugin Engine Admin Class
 *
 *
 */
class Pngx__Admin__Main {

	/*
	* Admin Construct
	*/
	public function __construct() {

		//Check to flush permalinks
		add_action( 'init', array( 'Pngx__Admin__Fields', 'flush_permalinks' ) );

		// Setup Ajax Methods for Editor
		new Pngx__Admin__Ajax();

		//Setup Admin
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );

	}

	/**
	 * Admin Init
	 */
	public static function admin_init() {

		//Register Admin Assets
		add_action( 'admin_enqueue_scripts', array( 'Pngx__Admin__Assets', 'register_assets' ), 0 );

		add_filter( 'pngx_visual_editor_functions', array( 'Cctor__Coupon__Pro__Admin__Fields', 'add_tiny_mce_buttons' ) );

		add_filter( 'pngx_html_editor_functions', array( 'Cctor__Coupon__Pro__Admin__Fields', 'add_html_editor_buttons' ) );

	} //end admin_init

}