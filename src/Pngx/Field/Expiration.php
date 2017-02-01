<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Field__Expiration' ) ) {
	return;
}


/**
 * Class Pngx__Field__Expiration
 * Text Field
 */
class Pngx__Field__Expiration {

	public static function display( $field = array(), $coupon_id = null, $meta = null, $template_fields = array() ) {

		$class = $field['display']['class'] ? ' class="' . $field['display']['class'] . ' " ' : ' ';
		$style = Pngx__Style__Linked::get_styles( $field, $coupon_id );
		$tags  = isset( $field['display']['tags'] ) ? $field['display']['tags'] : 'title';
		$wrap  = isset( $field['display']['wrap'] )  ? $field['display']['wrap'] : 'div';

		?>

		<?php echo $wrap ? '<' . esc_attr( $wrap ) .  $class .  $style . '>' : ''; ?>

		<?php echo 'future home of displaying expiration' . strip_tags( $meta, Pngx__Allowed_Tags::$tags() ); ?>

		<?php echo $wrap ? '</' .  esc_attr( $wrap ) . '>' : ''; ?>

		<?php

	}

}
