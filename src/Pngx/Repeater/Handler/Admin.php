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

	/**
	 * Display Opening HTML Wrap for Admin Fields
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_open( $i, $field_type ) {

		echo '
			<div class="pngx-wrapper ' . esc_attr( $field_type ) . '">
			<span class="add-repeater button"
			   data-repeater="' . $i . '>-repeater"
			>+</span>';

		if ( 'section' === $field_type ) {
			echo '
				<ul
					id="' . $i . '-repeater"
					class="pngx-repeater pngx-repeater-container repeating-section"
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
					class="pngx-repeater pngx-repeater-container repeating-column"
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
					class="pngx-repeater pngx-repeater-container repeating-field"
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

	/**
	 * Display Closing HTML Wrap for Admin Fields
	 *
	 * @param $i
	 */
	public function display_repeater_close( $i ) {

		echo '
				</ul>
			</div>
			';

		return;

	}

	/**
	 * Display Open Item HTML Wrap and Sort Handler
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_item_open( $i, $field_type, $class = null ) {

		if ( 'section' === $field_type ) {
			echo '<li class="repeater-item repeater-section ' . esc_attr( $class ) . '">
					<span class="repeater-sort">|||</span>';
		} elseif ( 'column' === $field_type ) {
			echo '<li class="repeater-item repeater-column ' . esc_attr( $class ) . '">
					<span class="repeater-sort">|||</span>';
		} elseif ( 'field' === $field_type && 'repeater-template' === $class ) {
			echo '<li class="repeating-field ' . esc_attr( $class ) . '">
					<span class="repeater-sort">|||</span>';
		}


		return;


	}

	/**
	 * Display Closing Item HTML Wrap
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_item_close( $i, $field_type ) {

		echo '
			<span class="remove-repeater button ' . esc_attr( $field_type ) . '"
			   data-repeater="' . $i . '-repeater"
			>X</span>
		</li>';

		return;

	}


	/**
	 * Display Closing Item HTML Wrap
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_item_clone_close( $i, $field_type ) {

		return '
			<span class="remove-repeater button"
			   data-repeater="' . $i . '-repeater"
			>X</span>
		</li>';

	}

	/**
	 * Display Admin Field
	 *
	 * @param $field
	 * @param $value
	 * @param $name
	 */
	public function display_field( $field, $value, $name ) {

		Pngx__Admin__Fields::display_field( $field, false, false, $value, $name );

		return;

	}

	public function display_repeater_field_open( $class = null ) {

		echo '<li class="repeating-field ' . esc_attr( $class ) . '">
				<span class="repeater-sort">|||</span>';

		return;

	}

	/**
	 * Display Admin Repeating Value Field
	 *
	 * @param $field
	 * @param $value
	 * @param $name
	 */
	public function display_repeater_field( $field, $value, $name, $post_id ) {

		echo Pngx__Admin__Fields::display_field( $field, false, false, $value, $name );

		return;

	}

	/**
	 * Handler used for Saving
	 *
	 * @param $post_id
	 * @param $id
	 * @param $new_meta
	 */
	public function post_cycle( $post_id, $id, $new_meta ) {

		return;
	}

}