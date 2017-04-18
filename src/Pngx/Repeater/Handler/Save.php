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

	public function display_repeater_open( $i, $field_type ) {

		return false;

	}

	public function display_repeater_close( $i ) {

		return false;

	}

	public function display_repeater_item_open( $i, $field_type ) {

		return false;

	}


	public function display_repeater_item_close( $i, $field_type ) {

		return false;

	}

	public function display_field( $field, $value ) {

		return false;

	}

	public function display_repeater_field( $field, $value ) {

		return false;

	}
}