<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Plugin Engine Repeater Admin Methods Handler
 *
 *
 */
class Pngx__Repeater__Handler__Admin {


	public function display_repeater_open( $i, $subkey, $field_type ) {

		return '<br>opendivmethod class="' . $i . ' ' . $subkey . '" <br>';

	}

	public function display_repeater_close( $i, $subkey, $field_type ) {

		return '/divmethod class="' . $i . ' ' . $subkey . '" <br>';

	}

}