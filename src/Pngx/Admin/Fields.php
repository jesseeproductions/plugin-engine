<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Meta' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Fields
 * Fields for Meta and Options
 */
class Pngx__Admin__Fields {

	/*
	* Toggle Field Data Setup
	*/
	public static function toggle( $toggle_fields, $id ) {

		if ( isset( $toggle_fields ) && is_array( $toggle_fields ) ) {

			foreach ( $toggle_fields as $key => $toggle_data ) {
				$toggle = '';
				if ( 'field' == $key ) {
					$toggle = esc_html( $toggle_data ) . '#' . esc_attr( $id );
				} elseif ( 'group' == $key || 'show' == $key || 'update_message' == $key ) {
					$toggle = esc_html( $toggle_data );
				} elseif ( 'id' == $key || 'wp_version' == $key  ) {
					$toggle = absint( $toggle_data );
				} elseif ( 'msg' == $key || 'tabs' == $key ) {
					$toggle = json_encode( $toggle_data, JSON_HEX_APOS );
				}

				//echo 'data-toggle-field="' . esc_html( $field['toggle_field'] ) . '#' . esc_html( $field['id'] ) . '"';
				//echo 'data-toggle-group="' . esc_html( $field['toggle_group'] ) . '"';
				//echo 'data-toggle-show="' . esc_html( $field['toggle_show'] ) . '"';
				//echo 'data-toggle-msg=\'' . json_encode( $field['toggle_msg'], JSON_HEX_APOS ) . '\'';

				return 'data-toggle-' . esc_attr( $key ) . '=\'' . $toggle . '\'';
			}

		}

	}

	/*
	* Option Fields
	*/
	public static function get_option_fields() {

		$fields['header_defaults'] = array(
			'section' => 'defaults',
			'title'   => '',
			'alert'   => __( '*These are defaults for new coupons only and do not change existing coupons.', 'coupon-creator' ),
			'type'    => 'heading'
		);
		//Expiration
		$fields['header_expiration'] = array(
			'section' => 'defaults',
			'title'   => '',
			'desc'    => __( 'Expiration', 'coupon-creator' ),
			'type'    => 'heading'
		);

		return $fields;

	}
		
	/*
	* Display Individual Fields
	*/
	public static function display_field( $option_args = array() ) {

			//Set for WP 4.3 and replacing wp_htmledit_pre
		global $wp_version;
		$cctor_required_wp_version = '4.3';

		$options = get_option( 'coupon_creator_options' );

		if ( ! isset( $options[ $option_args['id'] ] ) && $option_args['type'] != 'checkbox' ) {
			$options[ $option_args['id'] ] = $option_args['std'];
		} elseif ( ! isset( $options[ $option_args['id'] ] ) ) {
			$options[ $option_args['id'] ] = 0;
		}


		switch ( $option_args['type'] ) {

			case 'help':

				$help_class = new Cctor__Coupon__Admin__Help();
				$help_class->display_help( $option_args['section'], 'cctor_coupon_page_coupon-options', 'coupon' );

				break;

			case 'heading':
				if ( $option_args['alert'] ) {
					echo '</td></tr><tr valign="top"><td colspan="2"><span class="description">' . $option_args['alert'] . '</span>';
				} else {
					echo '</td></tr><tr valign="top"><td colspan="2"><h4>' . $option_args['desc'] . '</h4>';
				}
				break;

			case 'text':
				if ( $option_args['alert'] != '' && cctor_options( $option_args['condition'] ) == 1 ) {
					echo '<div class="cctor-error">' . $option_args['alert'] . '</div>';
				}

				echo '<input class="regular-text' . $option_args['class'] . '" type="text" id="' . $option_args['id'] . '" name="coupon_creator_options[' . $option_args['id'] . ']" placeholder="' . $option_args['std'] . '" value="' . esc_attr( $options[ $option_args['id'] ] ) . '" size="' . $option_args['size'] . '" />';

				if ( $option_args['desc'] != '' ) {
					echo '<br /><span class="description">' . $option_args['desc'] . '</span>';
				}

				break;

			case 'checkbox':

				echo '<input class="checkbox' . $option_args['class'] . '" type="checkbox" id="' . $option_args['id'] . '" name="coupon_creator_options[' . $option_args['id'] . ']" value="1" ' . checked( $options[ $option_args['id'] ], 1, false ) . ' /> <label for="' . $option_args['id'] . '">' . $option_args['desc'] . '</label>';

				break;

			// color
			case 'color':

				$default_color = '';
				if ( isset( $option_args['std'] ) ) {
					if ( $options[ $option_args['id'] ] != $option_args['std'] ) {
						$default_color = ' data-default-color="' . $option_args['std'] . '" ';
					}
				}

				echo '<input class="color-picker ' . $option_args['class'] . '" type="text" id="' . $option_args['id'] . '" name="coupon_creator_options[' . $option_args['id'] . ']" placeholder="' . $option_args['std'] . '" value="' . esc_attr( $options[ $option_args['id'] ] ) . '"' . $default_color . ' /><br /><span class="description">' . $option_args['desc'] . '</span>';

				break;

			case 'select':

				$cctor_select_value = $options[ $option_args['id'] ] ? $options[ $option_args['id'] ] : $option_args['std'];

				echo '<select class="select ' . $option_args['class'] . '" name="coupon_creator_options[' . $option_args['id'] . ']">';

				foreach ( $option_args['choices'] as $value => $label ) {

					$cctor_option_style = $option_args['class'] == 'css-select' ? 'style="' . esc_attr( $value ) . '"' : '';

					echo '<option ' . $cctor_option_style . ' value="' . esc_attr( $value ) . '"' . selected( $cctor_select_value, $value, false ) . '>' . esc_attr( $label ) . '</option>';

				}

				echo '</select>';

				if ( $option_args['desc'] != '' ) {
					echo '<br /><span class="description">' . $option_args['desc'] . '</span>';
				}

				break;

			case 'radio':
				$i = 0;
				foreach ( $option_args['choices'] as $value => $label ) {
					echo '<input class="radio' . $option_args['class'] . '" type="radio" name="coupon_creator_options[' . $option_args['id'] . ']" id="' . $option_args['id'] . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[ $option_args['id'] ], $value, false ) . '> <label for="' . $option_args['id'] . $i . '">' . esc_attr( $label ) . '</label>';
					if ( $i < count( $options ) - 1 ) {
						echo '<br />';
					}
					$i ++;
				}

				if ( $option_args['desc'] != '' ) {
					echo '<br /><span class="description">' . $option_args['desc'] . '</span>';
				}

				break;

			case 'textarea':
				global $wp_version;
				if ( version_compare( $wp_version, $cctor_required_wp_version, '<' ) ) {
					echo '<textarea class="' . $option_args['class'] . '" id="' . $option_args['id'] . '" name="coupon_creator_options[' . $option_args['id'] . ']" placeholder="' . $option_args['std'] . '" rows="12" cols="50">' . wp_htmledit_pre( $options[ $option_args['id'] ] ) . '</textarea>';
				} else {
					echo '<textarea class="' . $option_args['class'] . '" id="' . $option_args['id'] . '" name="coupon_creator_options[' . $option_args['id'] . ']" placeholder="' . $option_args['std'] . '" rows="12" cols="50">' . format_for_editor( $options[ $option_args['id'] ] ) . '</textarea>';
				}

				if ( $option_args['desc'] != '' ) {
					echo '<br /><span class="description">' . $option_args['desc'] . '</span><br />';
				}
				break;

			case 'help':

				$help_class = new Cctor__Coupon__Admin__Help();
				$help_class->display_help( 'all', false, 'coupon' );
				echo Cctor__Coupon__Admin__Help::get_cctor_support_core_contact();

				break;

			case 'license':

				$cctor_license_info = array();
				$cctor_license      = "cctor_" . $option_args['class'];
				$cctor_license_info = get_option( $cctor_license );

				echo '<input class="regular-text' . $option_args['class'] . '" type="text" id="' . $option_args['id'] . '" name="coupon_creator_options[' . $option_args['id'] . ']" placeholder="' . $option_args['std'] . '" value="' . esc_attr( $cctor_license_info['key'] ) . '" size="' . $option_args['size'] . '"/>';

				if ( $option_args['desc'] != '' ) {
					echo '<br /><span class="description">' . $option_args['desc'] . '</span>';
				}
				break;

			case 'license_status':

				$cctor_license_info = array();
				$cctor_license      = "cctor_" . $option_args['class'];

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

					echo '<input type="hidden" class="cctor_license_key" name="cctor_license_key" value="cctor_' . esc_attr( $option_args['class'] ) . '"/>';
					echo '<input type="hidden" class="cctor_license_name" name="cctor_license_name" value="' . esc_attr( $option_args['condition'] ) . '"/>';
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

					echo '<input type="hidden" class="cctor_license_key" name="cctor_license_key" value="cctor_' . esc_attr( $option_args['class'] ) . '"/>';
					echo '<input type="hidden" class="cctor_license_name" name="cctor_license_name" value="' . esc_attr( $option_args['condition'] ) . '"/>';
					echo '<input type="submit" class="cctor-license-button-det" name="cctor_license_activate" value="' . __( 'Activate License' ) . '"/>';

				}

				break;
		}

		if ( has_filter( 'cctor_option_cases' ) ) {
			/**
			 * Filter the cases for Coupon Creator Meta
			 *
			 * @param array $options     current coupon option field being displayed.
			 * @param array $option_args current value of option saved.
			 */
			echo apply_filters( 'cctor_option_cases', $options, $option_args );
		}
	}


}
