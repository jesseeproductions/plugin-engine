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
 * Text Field
 */
class Pngx__Admin__Field__Color {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		//$default_color = '';
		//if ( isset( $field['std'] ) ) {
		//	if ( $options[ $field['id'] ] != $field['std'] ) {
		//		$default_color = $field['std'];
		//	}
		//}

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

		echo '<input type="text" class="pngx-color-picker ' . esc_attr( $class ) . '"  id="' . $field['id'] . '" name="' . esc_attr( $name ) . '" placeholder="' . esc_attr( $std ) . '" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $std ) . '"" />';

		if ( "" != $field['desc'] ) {
			echo '<br /><span class="description">' . $field['desc'] . '</span>';
		}

	}

}

