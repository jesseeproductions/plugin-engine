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

	public static function display( $field = array(), $couponid = null, $meta = null ) {

		echo 'visual field<br>';
		echo $meta . '<br>';

	}

}
