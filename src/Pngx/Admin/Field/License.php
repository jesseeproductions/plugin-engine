<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__License' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__License
 * License Field only works on WordPress Options Page
 */
class Pngx__Admin__Field__License {

	public static function display( $field = array(), $options_id = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name         = $options_id;
			$license      = $field['class'];
			$license_info = get_option( $license );
			$value        = isset( $license_info['key'] ) ? $license_info['key'] : '';

			$size      = isset( $field['size'] ) ? $field['size'] : 30;
			$class     = isset( $field['class'] ) ? $field['class'] : '';
			$std       = isset( $field['std'] ) ? $field['std'] : '';

			echo '<input type="text" class="license-field ' . esc_attr( $class ) . '"  id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $name ) . '" placeholder="' . esc_attr( $std ) . '" value="' . esc_attr( $value ) . '" size="' . absint( $size ) . '" />';

			if ( "" != $field['desc'] ) {
				echo '<br /><span class="description">' . $field['desc'] . '</span>';
			}
		}

	}

}
