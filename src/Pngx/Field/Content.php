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
		$style = Pngx__Style__Linked::get_styles( $field, $couponid );
		$tags  = $field['display']['tags'];
		$wrap  = $field['display']['wrap'];

		?>

		<?php echo $wrap ? '<' . $wrap . $class . $style . '>' : ''; ?>

		<?php echo strip_tags( $meta, Pngx__Allowed_Tags::$tags() ); ?>

		<?php echo $wrap ? '</' . $wrap . '>' : ''; ?>

		<?php

	}

}
