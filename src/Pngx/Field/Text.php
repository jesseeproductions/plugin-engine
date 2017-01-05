<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Field__Text' ) ) {
	return;
}


/**
 * Class Pngx__Field__Text
 * Wysiwyg Field
 */
class Pngx__Field__Text {

	public static function display( $field = array(), $couponid = null, $meta = null ) {

		echo 'text field<br>';
		echo $meta . '<br>';

	}

}
