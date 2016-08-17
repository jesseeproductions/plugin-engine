<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Help' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Help
 * Help Fields
 */
class Pngx__Admin__Field__Help {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		$help_class = new Cctor__Coupon__Admin__Help();
		$help_class->display_help( $field['section'], 'cctor_coupon_page_coupon-options', 'coupon' );


		$help_class = new Cctor__Coupon__Admin__Help();
		$help_class->display_help( 'all', false, 'coupon' );
		echo Cctor__Coupon__Admin__Help::get_cctor_support_core_contact();


	}

}
