<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Field__Image' ) ) {
	return;
}


/**
 * Class Pngx__Field__Image
 * Wysiwyg Field
 */
class Pngx__Field__Image {

	public static function display( $field = array(), $coupon_id = null, $meta = null, $template_fields = array(), $var = array() ) {

		$class = $field['display']['class'] ? ' class="' . $field['display']['class'] . ' " ' : ' ';
		$style = Pngx__Style__Linked::get_styles( $field, $coupon_id );
		$tags  = isset( $field['display']['tags'] ) ? $field['display']['tags'] : 'title';
		$wrap  = isset( $field['display']['wrap'] ) ? $field['display']['wrap'] : 'div';

		$cctor_img_size = 'full';
		$couponimage_id = get_post_meta( $coupon_id, $field['id'], true );
		$couponimage    = wp_get_attachment_image_src( $couponimage_id, $cctor_img_size );
		$couponimage    = $couponimage[0];

		?>

		<?php echo $wrap ? '<' . esc_attr( $wrap ) . $class . $style . '>' : ''; ?>

		<img src='<?php echo esc_url( $couponimage ); ?>' alt='<?php echo get_the_title(); ?>' title='<?php echo get_the_title(); ?>'>

		<?php echo $wrap ? '</' . esc_attr( $wrap ) . '>' : ''; ?>

		<?php

	}

}
