<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Url' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Url
 * Text Field
 */
class Pngx__Admin__Field__Url {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		$size  = isset( $field['size'] ) ? $field['size'] : 30;
		$class = isset( $field['class'] ) ? $field['class'] : '';

		echo '<input type="text" class="url ' . esc_attr( $class ) . '"  id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $name ) . '" value="' . esc_url( $value ) . '" size="' . absint( $size ) . '" />';

		if ( isset( $field['desc'] ) && "" != $field['desc'] ) {
			echo '<br /><span class="description">' . $field['desc'] . '</span>';
		}

	}

}