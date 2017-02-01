<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Fields' ) ) {
	return;
}


/**
 * Class Pngx__Fields
 * Front End Fields for Meta and Options
 */
class Pngx__Fields {

	/**
	 * Display Individual Fields
	 *
	 * @param array $field     meta field attributes
	 * @param int   $coupon_id single coupon id
	 */
	public static function display_field( $field = array(), $coupon_id = null, $template_fields = array() ) {

		// get value of this field if it exists for this post
		$meta = get_post_meta( $coupon_id, $field['id'], true );

		switch ( $field['display']['type'] ) {

			case 'content':

				Pngx__Field__Content::display( $field, $coupon_id, $meta, $template_fields );

				break;

			case 'expiration':

				Pngx__Field__Expiration::display( $field, $coupon_id, $meta, $template_fields );

				break;

			case 'icon':

				Pngx__Field__Icon::display( $field, $coupon_id, $meta, $template_fields );

				break;

			case 'variety':

				Pngx__Field__Variety::display( $field, $coupon_id, $meta, $template_fields );

				break;
		}

		if ( has_filter( 'pngx_front_field_types' ) ) {
			/**
			 * Filter the Plugin Engine Fields for Front End
			 *
			 * @param array $field     current field attributes
			 * @param array $coupon_id current id
			 * @param array $meta      current value of field
			 */
			apply_filters( 'pngx_front_field_types', $field, $coupon_id, $meta );
		}

	}

}
