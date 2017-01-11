<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Style__Linked' ) ) {
	return;
}


/**
 * Class Pngx__Style__Linked
 * Front End Linked Style Fields
 */
class Pngx__Style__Linked {

	public static function get_styles( $field = array(), $couponid = null ) {

		$style = '';

		if ( isset( $field['styles'] ) && is_array( $field['styles'] ) ) {
			$style = ' style=" ';
			foreach ( $field['styles'] as $type => $field_name ) {

				if ( 'font-color' === $type && $color = get_post_meta( $couponid, $field_name, true ) ) {
					$style .= 'color:' . $color . '; ';
				}

				if ( 'background-color' === $type && $color = get_post_meta( $couponid, $field_name, true ) ) {
					$style .= 'background-color:' . $color . '; ';
				}

			}
			$style .= ' " ';
		}

		return $style;

	}

}
