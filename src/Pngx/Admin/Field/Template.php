<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Template' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Template
 * Ajax Template Scripts
 */
class Pngx__Admin__Field__Template {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		global $post;

		wp_localize_script( 'pngx-load-template-ajax', 'pngx_admin_ajax', array(
			'ajaxurl'        => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
			'nonce'          => wp_create_nonce( 'pngx_admin_' . $post->ID ),
			'coupon_version' => Cctor__Coupon__Main::VERSION_NUM,
			'post_id'        => $post->ID
		) );

	}

}


?>