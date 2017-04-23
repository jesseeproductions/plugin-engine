<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Plugin Engine Repeater Front End Methods Handler
 *
 *
 */
class Pngx__Repeater__Handler__Front_End {

	public function display_repeater_open( $i, $field_type ) {

		if ( 'section' === $field_type ) {
			echo '<ul
				id="' . $i . '-repeater"
				class="pngx-repeater repeating-section"
				data-name_id="wpe_menu_section"
				data-ajax_field_id="' . $i . '"
				data-ajax_action="pngx_repeater"
				data-repeat-type="section"
				data-column=0
		>';
		} elseif ( 'column' === $field_type ) {

			echo '
					<ul
							id="' . $i . '"
							class="pngx-repeater repeating-column"
							data-name_id="wpe_menu_section[wpe_menu_column][0]"
							data-ajax_field_id="' . $i . '"
							data-ajax_action="pngx_repeater"
							data-repeat-type="column"
							data-section=0
							data-column=0
					>';
		} elseif ( 'field' === $field_type ) {

			echo '
					<ul
							id="' . $i . '"
							class="pngx-repeater repeating-field"
							data-name_id="wpe_menu_section[wpe_menu_column][0]"
							data-ajax_field_id="' . $i . '"
							data-ajax_action="pngx_repeater"
							data-repeat-type="column"
							data-section=0
							data-column=0
					>';
		}


		return;


	}

	public function display_repeater_close( $i ) {

		echo '</ul>';

		return;

	}

	public function display_repeater_item_open( $i, $field_type ) {

		if ( 'section' === $field_type ) {
			echo '
			<li class="repeater-item repeater-section">';
		} elseif ( 'column' === $field_type ) {
			echo '
			<li class="repeater-item repeater-column 0">';
		}


		return;


	}


	public function display_repeater_item_close( $i, $field_type ) {

		echo '</li>';

		return;

	}

	public function display_field( $field, $value, $name, $post_id ) {

		//Pngx__Admin__Fields::display_field( $field, false, false, $value, $name );

		Pngx__Fields::display_field( $field, $post_id, $fields, array() );

		return;

	}

	public function display_repeater_field( $field, $value, $name ) {

		echo '<li class="repeating-field">' . Pngx__Admin__Fields::display_field( $field, false, false, $value, $name ) . '</li>';

		return;

	}

	public function post_cycle( $post_id, $id, $new_meta ) {

		return;
	}

}