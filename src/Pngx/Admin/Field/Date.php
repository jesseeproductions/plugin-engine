<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Date' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Date
 * Date Field
 */
class Pngx__Admin__Field__Date {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		$size  = isset( $field['size'] ) ? $field['size'] : 10;
		$class = isset( $field['class'] ) ? $field['class'] : '';
		$repeating = isset( $field['repeating'] ) ? '[]' : '';

		echo '<input type="text" class="pngx-datepicker ' . esc_attr( $class ) . '"  id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $name ) . $repeating . '" value="' . esc_attr( $value ) . '" size="' . absint( $size ) . '" />';

		if ( '' != $field['desc'] ) {
			echo '<br><span class="description">' . $field['desc'] . '</span>';
		}

		if ( isset( $field['condition'] ) && 'show_current_date' == $field['condition'] ) {

			$date = Pngx__Date::display_date( $field['format'] );

			if ( $date ) {
				echo '<br><span class="description">' . __( 'Today\'s Date is ', 'plugin-engine' ) . $date . '</span>';
			}

		}


	}

}
