<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Plugin Engine Repeater Save Methods Handler
 *
 *
 */
class Pngx__Repeater__Handler__Save {

	public function display_repeater_open( $i, $subkey, $field_type ) {

		return false;

	}

	public function display_repeater_close( $i, $subkey, $field_type ) {

		return false;

	}


	public function display_field( $i, $subkey, $field_type ) {

		return false;


	}
}