<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Field__Field__Variety' ) ) {
	return;
}


/**
 * Class Pngx__Field__Field__Variety
 * Text Field
 */
class Pngx__Field__Field__Variety {

	public static function display( $field = array(), $coupon_id = null, $meta = null, $template_fields = array(), $var = array() ) {

		if ( ! isset( $field['variety_choices'][ $meta ] ) ) {
			return;
		}

		$class = $field['display']['class'] ? ' class="' . $field['display']['class'] . ' " ' : ' ';
		$style = Pngx__Style__Linked::get_styles( $field, $coupon_id );
		//$tags  = isset( $field['display']['tags'] ) ? $field['display']['tags'] : 'title';
		$wrap = isset( $field['display']['wrap'] ) ? $field['display']['wrap'] : 'div';

		?>

		<?php echo $wrap ? '<' . esc_attr( $wrap ) .  $class .  $style . '>' : ''; ?>

		<?php

		foreach ( $field['variety_choices'][ $meta ] as $variety_fields ) {

			if ( isset( $template_fields[ $variety_fields ] ) ) {

				Pngx__Fields::display_field( $template_fields[ $variety_fields ], $coupon_id, $template_fields, $var );

			}

		}


		?>

		<?php echo $wrap ? '</' . esc_attr( $wrap ) . '>' : ''; ?>

		<?php

	}

}
