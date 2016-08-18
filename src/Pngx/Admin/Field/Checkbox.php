<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Checkbox' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Checkbox
 * Checkbox Field
 */
class Pngx__Admin__Field__Checkbox {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		global $pagenow;
		$selected = '';

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name     = $options_id;
			$selected = $options[ $field['id'] ];
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
		$std   = isset( $field['std'] ) ? $field['std'] : '';

		echo '<input type="checkbox" class="checkbox ' . esc_attr( $class ) . '"  id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $name ) . '" placeholder="' . esc_attr( $std ) . '"  value="1" ' . checked( $selected, 1, false ) . ' />';

		echo '<label for="' . esc_attr( $field['id'] ) . '">' . $field['desc'] . '</label>';

	}

}
