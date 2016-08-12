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

			foreach ( $toggle_fields as $key => $toggle_data ) {
				$toggle = '';
				if ( 'field' == $key ) {
					$toggle = esc_html( $toggle_data ) . '#' . esc_attr( $id );
				} elseif ( 'group' == $key || 'show' == $key || 'update_message' == $key ) {
					$toggle = esc_html( $toggle_data );
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

				return 'data-toggle-' . esc_attr( $key ) . '=\'' . $toggle . '\'';
			}

		}

		return false;
	}

	/*
	* Flush Permalink on Permalink Field Change
	*
	*/
	public static function flush_permalinks() {

		if ( get_option( 'cctor_coupon_base_change' ) == true || get_option( 'cctor_coupon_category_base_change' ) == true ) {

			//Coupon_Creator_Plugin::cctor_register_post_types();
			flush_rewrite_rules();
			update_option( 'coupon_flush_perm_change', date( 'l jS \of F Y h:i:s A' ) );
			update_option( 'cctor_coupon_base_change', false );
			update_option( 'cctor_coupon_category_base_change', false );
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


				break;

			// color
			case 'color':


				break;

			case 'heading':


				break;

			case 'help':


				break;

			case 'license':


				break;

			case 'license_status':


				break;

			case 'message':

				break;

			case 'pro':


				break;

			case 'radio':


				break;


			case 'select':


				break;

			case 'text':

				Pngx__Admin__Field__Text::display( $field, $options, $options_id, $meta );

				break;

			case 'textarea':

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
