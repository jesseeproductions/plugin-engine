<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Field__Content' ) ) {
	return;
}


/**
 * Class Pngx__Field__Content
 * Wysiwyg Field
 */
class Pngx__Field__Content {

	public static function display( $field = array(), $couponid = null, $meta = null ) {

		$class = $field['display']['class'] ? ' class="' . $field['display']['class'] . ' " ' : ' ';
		$style = '';
		$tags  = $field['display']['tags'];
		$wrap  = $field['display']['wrap'];

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

		?>

		<?php echo $wrap ? '<' . $wrap . $class . $style . '>' : ''; ?>

		<?php echo strip_tags( $meta, Pngx__Allowed_Tags::$tags() ); ?>

		<?php echo $wrap ? '</' . $wrap . '>' : ''; ?>

		<?php

	}

}
