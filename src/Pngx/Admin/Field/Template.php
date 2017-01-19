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

		$settings = array();
		if ( ! class_exists( '_WP_Editors' ) ) {
			require( ABSPATH . WPINC . '/class-wp-editor.php' );
		}
		$set = _WP_Editors::parse_settings( 'apid', $settings );
		if ( ! current_user_can( 'upload_files' ) ) {
			$set['media_buttons'] = false;
		}
		if ( $set['media_buttons'] ) {
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'media-upload' );
			$post = get_post();
			if ( ! $post && ! empty( $GLOBALS['post_ID'] ) ) {
				$post = $GLOBALS['post_ID'];
			}
			wp_enqueue_media( array(
				'post' => $post
			) );
		}

		_WP_Editors::editor_settings( 'apid', $set );
		$ap_vars = array(
			'url'          => get_home_url(),
			'includes_url' => includes_url()
		);

		wp_localize_script( 'pngx-wp-editor', 'ap_vars', $ap_vars );

		wp_localize_script( 'pngx-load-template-ajax', 'pngx_admin_ajax', array(
			'ajaxurl'                 => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
			'nonce'                   => wp_create_nonce( 'pngx_admin_' . $post->ID ),
			'coupon_version'          => Cctor__Coupon__Main::VERSION_NUM,
			'post_id'                 => $post->ID
		) );

	}

}


?>