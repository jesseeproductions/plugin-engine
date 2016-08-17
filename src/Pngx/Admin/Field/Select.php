<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Select' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Select
 * Select Field
 */
class Pngx__Admin__Field__Select {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		global $pagenow;
		$selected = '';

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name     = $options_id;
			$selected = $options[ $field['id'] ] ? $options[ $field['id'] ] : $field['std'];
		} else {
			$name = $field['id'];

			//Set Meta Default
			if ( $meta ) {
				$selected = $meta;
			} elseif ( $pagenow == 'post-new.php' && isset( $field['value'] ) ) {
				$selected = $field['value'];
			}
		}


		if ( $meta ) {
			$selected = $meta;
		} elseif ( $pagenow == 'post-new.php' && isset( $field['value'] ) ) {
			$selected = $field['value'];
		}

		$class = isset( $field['class'] ) ? $field['class'] : '';

		echo '<select class="select ' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '">';

		foreach ( $field['choices'] as $value => $label ) {

			$style = isset( $field['class'] ) && 'css-select' == $field['class'] ? 'style="' . esc_attr( $value ) . '"' : '';

			echo '<option ' . $style . ' value="' . esc_attr( $value ) . '"' . selected( $selected, $value, false ) . '>' . esc_attr( $label ) . '</option>';

		}

		echo '</select>';

	}

}
