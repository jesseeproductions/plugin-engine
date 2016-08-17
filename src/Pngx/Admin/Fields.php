<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Fields' ) ) {
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

			$data = '';

			foreach ( $toggle_fields as $key => $toggle_data ) {
				$toggle = '';
				if ( 'field' == $key ) {
					$toggle = esc_html( $toggle_data ) . '#' . esc_attr( $id );
				} elseif ( 'group' == $key || 'show' == $key || 'update_message' == $key ) {

					//handle options page update message in array
					if ( is_array( $toggle_data ) ) {
						if ( isset( $toggle_data[0]['code'] ) ) {
							$toggle = esc_html( $toggle_data[0]['code'] );
						}
					} else {
						$toggle = esc_html( $toggle_data );
					}
				} elseif ( 'id' == $key || 'wp_version' == $key ) {
					$toggle = absint( $toggle_data );
				} elseif ( 'msg' == $key || 'tabs' == $key ) {
					$toggle = json_encode( $toggle_data, JSON_HEX_APOS );
				}

				//todo remove
				//echo 'data-toggle-field="' . esc_html( $field['toggle_field'] ) . '#' . esc_html( $field['id'] ) . '"';
				//echo 'data-toggle-group="' . esc_html( $field['toggle_group'] ) . '"';
				//echo 'data-toggle-show="' . esc_html( $field['toggle_show'] ) . '"';
				//echo 'data-toggle-msg=\'' . json_encode( $field['toggle_msg'], JSON_HEX_APOS ) . '\'';

				$data .= 'data-toggle-' . esc_attr( $key ) . '=\'' . $toggle . '\' ';
			}

			return $data;

		}

		return false;
	}

	/*
	* Flush Permalink on Permalink Field Change
	*
	*/
	public static function flush_permalinks() {

		if ( true == get_option( 'pngx_permalink_change' ) ) {
			log_me( 'flush_permalinks' );
			//Coupon_Creator_Plugin::cctor_register_post_types();
			flush_rewrite_rules();
			update_option( 'pngx_permalink_flush', date( 'l jS \of F Y h:i:s A' ) );
			update_option( 'pngx_permalink_change', false );

		}

	}

	/*
	* Display Individual Fields
	*/
	public static function display_field( $field = array(), $options = array(), $options_id = null, $meta = null, $tab_slug = null, $post = null, $wp_version ) {

		//Create Different Name for Option Fields and Not Meta Fields
		if ( $options ) {
			$options_id = $options_id . '[' . $field['id'] . ']';
		}

		switch ( $field['type'] ) {

			case 'checkbox':

				Pngx__Admin__Field__Checkbox::display( $field, $options, $options_id, $meta );

				break;

			case 'color':

				Pngx__Admin__Field__Color::display( $field, $options, $options_id, $meta );

				break;

			case 'date':

				Pngx__Admin__Field__Date::display( $field, $options, $options_id, $meta );

				break;

			case 'heading':

				Pngx__Admin__Field__Heading::display( $field, $options, $options_id, $meta );

				break;

			case 'help':

				Pngx__Admin__Field__Help::display( $field, $options, $options_id, $meta );

				break;

			case 'license':


				break;

			case 'license_status':


				break;

			case 'message':

				Pngx__Admin__Field__Message::display( $field, $options, $options_id, $meta );

				break;

			case 'pro':


				break;

			case 'radio':

				Pngx__Admin__Field__Radio::display( $field, $options, $options_id, $meta );

				break;


			case 'select':

				Pngx__Admin__Field__Select::display( $field, $options, $options_id, $meta );

				break;

			case 'text':

				Pngx__Admin__Field__Text::display( $field, $options, $options_id, $meta );

				break;

			case 'textarea':

				Pngx__Admin__Field__Textarea::display( $field, $options, $options_id, $meta, $wp_version );

				break;


			case 'url':


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

}
