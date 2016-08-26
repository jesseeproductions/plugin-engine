<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__License_Status' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__License_Status
 * License Status Field
 */
class Pngx__Admin__Field__License_Status {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		$expiration_msg = $expiration_date = '';
		$license        = $field['class'];
		$license_info   = get_option( $license );

		if ( isset( $license_info['expires'] ) ) { // Only Display Expiration if Date
			$expiration_date = strtotime( $license_info['expires'] );
			$expiration_date = date( get_option( 'date_format' ), $expiration_date );
			$expiration_msg  = sprintf( __( ' and Expires on %s', 'plugin-engine' ), esc_attr( $expiration_date ) );
		}

		echo '<input type="hidden" class="pngx_license_key" name="pngx_license_key" value="' . esc_attr( $license ) . '"/>';
		echo '<input type="hidden" class="pngx_license_name" name="pngx_license_name" value="' . esc_attr( $field['condition'] ) . '"/>';

		if ( isset( $license_info['status'] ) && false !== $license_info['status'] && 'valid' == $license_info['status'] ) {

			echo '<span style="color:green;">' . __( 'License is Active', 'plugin-engine' ) . $expiration_msg . '</span><br><br>';

			wp_nonce_field( 'pngx_license_nonce', 'pngx_license_nonce' );

			echo '<input type="submit" class="pngx-license-button-act" name="pngx_license_deactivate" value="' . _( 'Deactivate License' ) . '"/>';

		} else {

			if ( isset( $license_info['status'] ) && ( 'invalid' == $license_info['status'] || 'missing' == $license_info['status'] ) && ! $license_info['expired'] ) {
				$license_info_valid = __( 'License is Invalid', 'plugin-engine' );
			} elseif ( isset( $license_info['expired'] ) && 'expired' == $license_info['expired'] ) {
				$license_info_valid = sprintf( __( 'License Expired on %s', 'plugin-engine' ), esc_attr( $expiration_date ) );
			} else {
				$license_info_valid = __( 'License is Not Active', 'plugin-engine' );
			}

			echo '<span style="color:red;">' . $license_info_valid . '</span><br><br>';

			wp_nonce_field( 'pngx_license_nonce', 'pngx_license_nonce' );

			echo '<input type="submit" class="pngx-license-button-det" name="pngx_license_activate" value="' . __( 'Activate License' ) . '"/>';

		}

	}

}
