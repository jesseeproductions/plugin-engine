<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Url' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Url
 * Text Field
 */
class Pngx__Admin__Field__Url {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		$size  = isset( $field['size'] ) ? $field['size'] : 30;
		$class = isset( $field['class'] ) ? $field['class'] : '';
		$std   = isset( $field['std'] ) ? $field['std'] : '';

		if ( isset( $field['alert'] ) && '' != $field['alert'] && 1 == cctor_options( $field['condition'] ) ) {
			echo '<div class="pngx-error">&nbsp;&nbsp;' . $field['alert'] . '</div>';
		}

		echo '<input type="text" class="regular-text ' . esc_attr( $class ) . '"  id="' . $field['id'] . '" name="' . esc_attr( $name ) . '" placeholder="' . esc_attr( $std ) . '" value="' . esc_attr( $value ) . '" size="' . absint( $size ) . '" />';

		if ( "" != $field['desc'] ) {
			echo '<br /><span class="description">' . $field['desc'] . '</span>';
		}

	}

}


/**
 * Class Pngx__Admin__Fields__Field__Text
 * Text Field
 */
class Pngx__Admin__Fields__Field__Text_Remove {

	/*
	* Display Individual Fields
	*/
	public static function display_field( $field = array(), $options = array(), $options_id = null, $wp_version ) {

		$options_id =  $options_id . '[' . $field['id'] . ']';

		switch ( $field['type'] ) {

			case 'license':

				$cctor_license_info = array();
				$cctor_license      = "cctor_" . $field['class'];
				$cctor_license_info = get_option( $cctor_license );

				echo '<input class="regular-text' . $field['class'] . '" type="text" id="' . $field['id'] . '" name="' . esc_attr( $options_id ) . '" placeholder="' . $field['std'] . '" value="' . esc_attr( $cctor_license_info['key'] ) . '" size="' . $field['size'] . '"/>';

				if ( $field['desc'] != '' ) {
					echo '<br /><span class="description">' . $field['desc'] . '</span>';
				}
				break;

			case 'license_status':

				$expiration_date = '';
				$cctor_license_info = array();
				$cctor_license      = "cctor_" . $field['class'];

				$cctor_license_info = get_option( $cctor_license );

				//Coupon Expiration Date
				if ( isset( $cctor_license_info['expires'] ) ) {
					$expirationco = $cctor_license_info['expires'];
				} else {
					$expirationco = '';;
				}

				$cc_expiration_date = strtotime( $expirationco );

				if ( $expirationco ) { // Only Display Expiration if Date
					$daymonth_date_format = cctor_options( 'cctor_default_date_format' ); //Date Format

					if ( $daymonth_date_format == 1 ) { //Change to Day - Month Style
						$expirationco = date( "d/m/Y", $cc_expiration_date );
					} else {
						$expirationco = date( "m/d/Y", $cc_expiration_date );
					}

					$expiration_date = sprintf( __( ' and Expires on %s', 'coupon-creator' ), esc_attr( $expirationco ) );
				}

				if ( isset( $cctor_license_info['status'] ) && $cctor_license_info['status'] !== false && $cctor_license_info['status'] == 'valid' ) {

					echo '<span style="color:green;">' . __( 'License is Active', 'coupon-creator' ) . $expiration_date . '</span><br><br>';

					wp_nonce_field( 'cctor_license_nonce', 'cctor_license_nonce' );

					echo '<input type="hidden" class="cctor_license_key" name="cctor_license_key" value="cctor_' . esc_attr( $field['class'] ) . '"/>';
					echo '<input type="hidden" class="cctor_license_name" name="cctor_license_name" value="' . esc_attr( $field['condition'] ) . '"/>';
					echo '<input type="submit" class="cctor-license-button-act" name="cctor_license_deactivate" value="' . _( 'Deactivate License' ) . '"/>';

				} else {
					$cctor_license_info_valid = "";
					if ( isset( $cctor_license_info['status'] ) && ( $cctor_license_info['status'] == 'invalid' || $cctor_license_info['status'] == 'missing' ) && ! $cctor_license_info['expired'] ) {
						$cctor_license_info_valid = __( 'License is Invalid', 'coupon-creator' );
					} elseif ( isset( $cctor_license_info['expired'] ) && $cctor_license_info['expired'] == "expired" ) {
						$cctor_license_info_valid = sprintf( __( 'License Expired on %s', 'coupon-creator' ), esc_attr( $expirationco ) );
					} else {
						$cctor_license_info_valid = __( 'License is Not Active', 'coupon-creator' );
					}

					echo '<span style="color:red;">' . $cctor_license_info_valid . '</span><br><br>';

					wp_nonce_field( 'cctor_license_nonce', 'cctor_license_nonce' );

					echo '<input type="hidden" class="cctor_license_key" name="cctor_license_key" value="cctor_' . esc_attr( $field['class'] ) . '"/>';
					echo '<input type="hidden" class="cctor_license_name" name="cctor_license_name" value="' . esc_attr( $field['condition'] ) . '"/>';
					echo '<input type="submit" class="cctor-license-button-det" name="cctor_license_activate" value="' . __( 'Activate License' ) . '"/>';

				}

				break;
		}

	}


	/*
	* Display Individual Fields
	*/
	public static function display_meta_field( $field = array(), $meta = null, $tab_slug = null, $post = null, $wp_version = null ) {


		switch ( $field['type'] ) {

				// url
				case 'url':
					?>
					<input type="text" name="<?php echo $field['id']; ?>"
					       id="<?php echo $field['id']; ?>"
					       value="<?php echo esc_url( $meta ); ?>" size="30"/>
					<br/><span class="description"><?php echo $field['desc']; ?></span>

					<?php break;




				// image using Media Manager from WP 3.5 and greater
				case 'image': ?>

					<?php //Check existing field and if numeric
					if ( is_numeric( $meta ) ) {
						$image = wp_get_attachment_image_src( $meta, 'medium' );
						$image = $image[0];
						$image = '<div style="display:none" id="' . $field['id'] . '" class="pngx-default-image pngx-image-wrap">' . $field['image'] . '</div> <img src="' . $image . '" id="' . $field['id'] . '" class="pngx-image pngx-image-wrap-img" />';
					} else {
						$image = '<div style="display:block" id="' . $field['id'] . '" class="pngx-default-image pngx-image-wrap">' . $field['image'] . '</div> <img style="display:none" src="" id="' . $field['id'] . '" class="pngx-image pngx-image-wrap-img" />';
					} ?>

					<?php echo $image; ?><br/>
					<input name="<?php echo $field['id']; ?>"
					       id="<?php echo $field['id']; ?>" type="hidden"
					       class="pngx-upload-image" type="text" size="36" name="ad_image"
					       value="<?php echo esc_attr( $meta ); ?>"
						/>
					<input id="<?php echo $field['id']; ?>" class="pngx-image-button"
					       type="button" value="Upload Image"/>
					<small><a href="#" id="<?php echo $field['id']; ?>"
					          class="pngx-clear-image">Remove Image</a>
					</small>
					<br/><span class="description"><?php echo $field['desc']; ?></span>

					<?php break;

				// Pro
				case 'cctor_pro':

					echo ! defined( 'CCTOR_HIDE_UPGRADE' ) || ! CCTOR_HIDE_UPGRADE ? Cctor__Coupon__Admin__Options::display_pro_section() : '';

					break;

			} //end switch

	}

}
