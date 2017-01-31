<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Text' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Text
 * Text Field
 */
class Pngx__Admin__Field__Text {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		$size      = isset( $field['size'] ) ? $field['size'] : 30;
		$class     = isset( $field['class'] ) ? $field['class'] : '';
		$std       = isset( $field['std'] ) ? $field['std'] : '';
		$condition = isset( $field['condition'] ) ? $field['condition'] : '';
		$repeating = isset( $field['repeating'] ) ? '[]' : '';

		if ( isset( $field['alert'] ) && '' != $field['alert'] && 1 == $condition ) {
			echo '<div class="pngx-error">&nbsp;&nbsp;' . $field['alert'] . '</div>';
		}

		echo '<input type="text" id="' . esc_attr( $field['id'] ) . '" class="regular-text ' . esc_attr( $class ) . '"  name="' . esc_attr( $name ) . $repeating . '" placeholder="' . esc_attr( $std ) . '" value="' . esc_attr( $value ) . '" size="' . absint( $size ) . '" />';

		if ( isset( $field['desc'] ) && "" != $field['desc'] ) {
			echo '<br /><span class="description">' . $field['desc'] . '</span>';
		}

	}

}
