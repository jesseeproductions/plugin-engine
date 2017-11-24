<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Post_ID' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Post_ID
 */
class Pngx__Admin__Field__Post_ID {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $repeat_name = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
			if ( ! $value && isset( $field['value'] ) ) {
				$value = $field['value'];
			}
		}

		if ( $repeat_name ) {
			$name = $repeat_name;
		}

		echo '<input type="hidden" id="' . esc_attr( $field['id'] ) . '" class="hidden-field"  name="' . esc_attr( $name ) . '"  value="' . esc_attr( $value ) . '" />';

	}

}
