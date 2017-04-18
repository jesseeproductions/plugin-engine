<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Plugin Engine Repeater Admin Methods Handler
 *
 *
 */
class Pngx__Repeater__Handler__Admin {


	public function display_repeater_open( $i, $field_type ) {

		if ( 'section' === $field_type ) {
			return '<ul
				id="' . $i . '-repeater"
				class="pngx-repeater repeating-section"
				data-name_id="wpe_menu_section"
				data-ajax_field_id="' . $i . '"
				data-ajax_action="pngx_repeater"
				data-repeat-type="section"
				data-column=0
		>';
		} elseif ( 'column' === $field_type ) {

			return '
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

			return '
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


		return false;


	}

	public function display_repeater_close( $i ) {

		return '</ul>';

	}

	public function display_repeater_item_open( $i, $field_type ) {

		if ( 'section' === $field_type ) {
			return '
			<li class="repeater-item repeater-section">
				<span class="sort hndle">|||</span>';
		} elseif ( 'column' === $field_type ) {
			return '
			<li class="repeater-item repeater-column 0">
				<span class="sort hndle">|||</span>';
		}


		return false;


	}


	public function display_repeater_item_close( $i, $field_type ) {

		return '
			<a class="add-repeater button"
			   data-repeater="' . $i . '>-repeater"
			   href="#"
			>+</a>
			<a class="remove-repeater button"
			   data-repeater="' . $i . '-repeater"
			   href="#"
			>X</a>
		</li>';

	}

	public function display_field( $field, $value, $name ) {

		Pngx__Admin__Fields::display_field( $field, false, false, $value, $name );

	}

	public function display_repeater_field( $field, $value, $name ) {

		return '<li class="repeating-field">' . Pngx__Admin__Fields::display_field( $field, false, false, $value, $name ) . '</li>';

	}

}