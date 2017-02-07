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

	public static function display( $field = array(), $coupon_id = null, $meta = null, $template_fields = array(), $var = array() ) {

		$class = $field['display']['class'] ? $field['display']['class'] : '';
		$style = Pngx__Style__Linked::get_styles( $field, $coupon_id );
		$tags  = isset( $field['display']['tags'] ) ? $field['display']['tags'] : 'title';
		$wrap  = isset( $field['display']['wrap'] ) ? $field['display']['wrap'] : 'div';

		?>

		<?php echo $wrap ? '<' . esc_attr( $wrap ) . ' class="' . esc_attr( $class ) . '" ' . $style . '>' : ''; ?>

		<?php echo strip_tags( $meta, Pngx__Allowed_Tags::$tags() ); ?>

		<?php echo $wrap ? '</' . esc_attr( $wrap ) . '>' : ''; ?>

		<?php

	}

}
