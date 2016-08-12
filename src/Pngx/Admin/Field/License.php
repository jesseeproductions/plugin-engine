Heading.php<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Fields__Field__Text' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Fields__Field__Text
 * Text Field
 */
class Pngx__Admin__Fields__Field__Text {

	/*
	* Display Individual Fields
	*/
	public static function display_field( $field = array(), $options = array(), $options_id = null, $wp_version ) {

		$options_id =  $options_id . '[' . $field['id'] . ']';

		switch ( $field['type'] ) {

			case 'help':

				$help_class = new Cctor__Coupon__Admin__Help();
				$help_class->display_help( $field['section'], 'cctor_coupon_page_coupon-options', 'coupon' );

				break;

			case 'heading':
				if ( $field['alert'] ) {
					echo '</td></tr><tr valign="top"><td colspan="2"><span class="description">' . $field['alert'] . '</span>';
				} else {
					echo '</td></tr><tr valign="top"><td colspan="2"><h4>' . $field['desc'] . '</h4>';
				}
				break;

			case 'text':
				if ( '' != $field['alert'] && 1 == cctor_options( $field['condition'] ) ) {
					echo '<div class="cctor-error">' . $field['alert'] . '</div>';
				}

				echo '<input class="regular-text' . $field['class'] . '" type="text" id="' . $field['id'] . '" name="' . esc_attr( $options_id ) . '" placeholder="' . $field['std'] . '" value="' . esc_attr( $options[ $field['id'] ] ) . '" size="' . $field['size'] . '" />';

				if ( $field['desc'] != '' ) {
					echo '<br /><span class="description">' . $field['desc'] . '</span>';
				}

				break;

			case 'checkbox':

				echo '<input class="checkbox' . $field['class'] . '" type="checkbox" id="' . $field['id'] . '" name="' . esc_attr( $options_id ) . '" value="1" ' . checked( $options[ $field['id'] ], 1, false ) . ' /> <label for="' . $field['id'] . '">' . $field['desc'] . '</label>';

				break;

			// color
			case 'color':

				$default_color = '';
				if ( isset( $field['std'] ) ) {
					if ( $options[ $field['id'] ] != $field['std'] ) {
						$default_color = ' data-default-color="' . $field['std'] . '" ';
					}
				}

				echo '<input class="color-picker ' . $field['class'] . '" type="text" id="' . $field['id'] . '" name="' . esc_attr( $options_id ) . '" placeholder="' . $field['std'] . '" value="' . esc_attr( $options[ $field['id'] ] ) . '"' . $default_color . ' /><br /><span class="description">' . $field['desc'] . '</span>';

				break;

			case 'select':

				$cctor_select_value = $options[ $field['id'] ] ? $options[ $field['id'] ] : $field['std'];

				echo '<select class="select ' . $field['class'] . '" name="' . esc_attr( $options_id ) . '">';

				foreach ( $field['choices'] as $value => $label ) {

					$cctor_option_style = $field['class'] == 'css-select' ? 'style="' . esc_attr( $value ) . '"' : '';

					echo '<option ' . $cctor_option_style . ' value="' . esc_attr( $value ) . '"' . selected( $cctor_select_value, $value, false ) . '>' . esc_attr( $label ) . '</option>';

				}

				echo '</select>';

				if ( $field['desc'] != '' ) {
					echo '<br /><span class="description">' . $field['desc'] . '</span>';
				}

				break;

			case 'radio':
				$i = 0;
				foreach ( $field['choices'] as $value => $label ) {
					echo '<input class="radio' . $field['class'] . '" type="radio" name="' . esc_attr( $options_id ) . '" id="' . $field['id'] . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[ $field['id'] ], $value, false ) . '> <label for="' . $field['id'] . $i . '">' . esc_attr( $label ) . '</label>';
					if ( $i < count( $options ) - 1 ) {
						echo '<br />';
					}
					$i ++;
				}

				if ( $field['desc'] != '' ) {
					echo '<br /><span class="description">' . $field['desc'] . '</span>';
				}

				break;

			case 'textarea':
				global $wp_version;
				if ( version_compare( $wp_version, '4.3', '<' ) ) {
					echo '<textarea class="' . $field['class'] . '" id="' . $field['id'] . '" name="' . esc_attr( $options_id ) . '" placeholder="' . $field['std'] . '" rows="12" cols="50">' . wp_htmledit_pre( $options[ $field['id'] ] ) . '</textarea>';
				} else {
					echo '<textarea class="' . $field['class'] . '" id="' . $field['id'] . '" name="' . esc_attr( $options_id ) . '" placeholder="' . $field['std'] . '" rows="12" cols="50">' . format_for_editor( $options[ $field['id'] ] ) . '</textarea>';
				}

				if ( $field['desc'] != '' ) {
					echo '<br /><span class="description">' . $field['desc'] . '</span><br />';
				}
				break;

			case 'help':

				$help_class = new Cctor__Coupon__Admin__Help();
				$help_class->display_help( 'all', false, 'coupon' );
				echo Cctor__Coupon__Admin__Help::get_cctor_support_core_contact();

				break;

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

		if ( has_filter( 'pngx_field_types' ) ) {
			/**
			 * Filter the Plugin Engine Fields for Meta and Options
			 *
			 * @param array $options current coupon field being displayed.
			 * @param array $field   current value of option saved.
			 */
			echo apply_filters( 'pngx_field_types', $options, $field );
		}
	}


	/*
	* Display Individual Fields
	*/
	public static function display_meta_field( $field = array(), $meta = null, $tab_slug = null, $post = null, $wp_version = null ) {


		switch ( $field['type'] ) {

				case 'heading':

					echo '<h4 class="pngx-fields-heading">'. $field["desc"].'</h4>';

					break;

				case 'message':
					?>

					<span class="description"><?php echo $field['desc']; ?></span>

					<?php break;

				// text
				case 'text':
					?>
					<?php if ( isset( $field['alert'] ) && $field['alert'] != '' && cctor_options( $field['condition'] ) == 1 ) {
					echo '<div class="pngx-error">&nbsp;&nbsp;' . $field['alert'] . '</div>';
				}
					?>
					<input type="text" name="<?php echo $field['id']; ?>"
					       id="<?php echo $field['id']; ?>"
					       value="<?php echo esc_attr( $meta ); ?>" size="30"/>
					<br/><span class="description"><?php echo $field['desc']; ?></span>

					<?php break;
				// url
				case 'url':
					?>
					<input type="text" name="<?php echo $field['id']; ?>"
					       id="<?php echo $field['id']; ?>"
					       value="<?php echo esc_url( $meta ); ?>" size="30"/>
					<br/><span class="description"><?php echo $field['desc']; ?></span>

					<?php break;
				// textarea
				case 'textarea': ?>
					<?php if ( version_compare( $wp_version, '4.3', '<' ) ) { ?>
						<textarea name="<?php echo $field['id']; ?>"
						          id="<?php echo $field['id']; ?>" cols="60"
						          rows="4"><?php echo wp_htmledit_pre( $meta ); ?></textarea>
						<br/><span class="description"><?php echo $field['desc']; ?></span>
					<?php } else { ?>
						<textarea name="<?php echo $field['id']; ?>"
						          id="<?php echo $field['id']; ?>" cols="60"
						          rows="4"><?php echo format_for_editor( $meta ); ?></textarea>
						<br/><span class="description"><?php echo $field['desc']; ?></span>
					<?php } ?>
					<?php break;

				// checkbox
				case 'checkbox':

					//Check for Default
					global $pagenow;
					$selected = '';
					if ( $meta ) {
						$selected = $meta;
					} elseif ( $pagenow == 'post-new.php' && isset( $field['value'] ) ) {
						$selected = $field['value'];
					}

					?>

					<input type="checkbox" name="<?php echo $field['id']; ?>"
					       id="<?php echo $field['id']; ?>" <?php echo checked( $selected, 1, false ); ?>/>
					<label
						for="<?php echo $field['id']; ?>"><?php echo $field['desc']; ?></label>

					<?php break;

				case 'select':

					//Check for Default
					global $pagenow;
					$selected = '';
					if ( $meta ) {
						$selected = $meta;
					} elseif ( $pagenow == 'post-new.php' ) {
						$selected = isset( $field['value'] ) ? $field['value'] : '';
					}

					?>
					<select id="<?php echo $field['id']; ?>"
					        class="select <?php echo $field['id']; ?>"
					        name="<?php echo $field['id']; ?>">

						<?php foreach ( $field['choices'] as $value => $label ) {

							echo '<option value="' . esc_attr( $value ) . '"' . selected( $value, $selected ) . '>' . $label . '</option>';

						} ?>
					</select>
					<span class="description"><?php echo $field['desc']; ?></span>

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
				// color
				case 'color': ?>
					<?php //Check if Values and If None, then use default
					if ( ! $meta ) {
						$meta = $field['value'];
					}
					?>
					<input class="pngx-color-picker" type="text"
					       name="<?php echo $field['id']; ?>"
					       id="<?php echo $field['id']; ?>"
					       value="<?php echo esc_attr( $meta ); ?>"
					       data-default-color="<?php echo $field['value']; ?>"/>
					<br/><span class="description"><?php echo $field['desc']; ?></span>

					<?php break;
				// date
				case 'date':

					//Blog Time According to WordPress
					$todays_date = "";
					if ( $field['id'] == "cctor_expiration" ) {
						$cc_blogtime = current_time( 'mysql' );

						list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = preg_split( '([^0-9])', $cc_blogtime );

						if ( cctor_options( 'cctor_default_date_format' ) == 1 || $meta == 1 ) {
							$today_first  = $today_day;
							$today_second = $today_month;
						} else {
							$today_first  = $today_month;
							$today_second = $today_day;
						}

						$todays_date = '<span class="description">' . __( 'Today\'s Date is ', 'plugin-engine' ) . $today_first . '/' . $today_second . '/' . $today_year . '</span>';
					}
					?>

					<input type="text" class="pngx-datepicker"
					       name="<?php echo $field['id']; ?>"
					       id="<?php echo $field['id']; ?>"
					       value="<?php echo esc_attr( $meta ); ?>" size="10"/>
					<br/><span class="description"><?php echo $field['desc']; ?></span>
					<?php echo $todays_date; ?>

					<?php break;
				// Help
				case 'help':

					/**
					 * Hook into help tab that display all help content for a plugin
					 */
					do_action( 'pngx-help-tab', $tab_slug );

					break;

				// Pro
				case 'cctor_pro':

					echo ! defined( 'CCTOR_HIDE_UPGRADE' ) || ! CCTOR_HIDE_UPGRADE ? Cctor__Coupon__Admin__Options::display_pro_section() : '';

					break;

			} //end switch

	}

}
