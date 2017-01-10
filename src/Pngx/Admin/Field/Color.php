<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Color' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Color
 * Color Field
 */
class Pngx__Admin__Field__Color {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
			if ( ! $value ) {
				$value = $field['value'];
			}
		}

		$class = isset( $field['class'] ) ? $field['class'] : '';
		$std   = isset( $field['std'] ) ? $field['std'] : '';

		echo '<input type="text" class="pngx-color-picker ' . esc_attr( $class ) . '"  id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $name ) . '" placeholder="' . esc_attr( $std ) . '" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $std ) . '"" data-alpha="true" />';

		if ( '' !== $field['desc'] ) {
			echo '<br /><span class="description">' . $field['desc'] . '</span>';
		}

	}

}

