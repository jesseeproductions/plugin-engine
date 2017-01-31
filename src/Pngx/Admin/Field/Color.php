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
			if ( ! $value && isset( $field['value'] ) ) {
				$value = $field['value'];
			}
		}

		$class     = isset( $field['class'] ) ? $field['class'] : '';
		$std       = isset( $field['std'] ) ? $field['std'] : '';
		$alpha     = isset( $field['alpha'] ) && 'true' === $field['alpha'] ? true : false;
		$repeating = isset( $field['repeating'] ) ? '[]' : '';

		echo '<input type="text" class="pngx-color-picker ' . esc_attr( $class ) . '"  id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $name ) . $repeating . '" placeholder="' . esc_attr( $std ) . '" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $std ) . '"" data-alpha="' . esc_attr( $alpha ) . '" />';

		if ( isset( $field['inside_label'] ) && '' !== $field['inside_label'] ) {
			echo '<label class="pngx-inside-label">' . esc_html( $field['inside_label'] ) . '</label>';
		}

		if ( '' !== $field['desc'] ) {
			echo '<br /><span class="description">' . esc_html( $field['desc'] ) . '</span>';
		}

	}

}

