<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Wysiwyg' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Wysiwyg
 * Text Field
 */
class Pngx__Admin__Field__Wysiwyg {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $wp_version ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		$std = isset( $field['std'] ) ? $field['std'] : '';

		if ( Pngx__Main::instance()->doing_ajax ) {
			$rows  = isset( $field['rows'] ) ? $field['rows'] : 12;
			$cols  = isset( $field['cols'] ) ? $field['cols'] : 50;
			$class = isset( $field['class'] ) ? $field['class'] : '';

			if ( version_compare( $wp_version, '4.3', '<' ) ) {
				echo '<textarea class="pngx-ajax-wp-editor ' . esc_attr( $class ) . '" id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $name ) . '" placeholder="' . esc_attr( $std ) . '" rows="' . absint( $rows ) . '" cols="' . absint( $cols ) . '">' . wp_htmledit_pre( $value ) . '</textarea>';
			} else {
				echo '<textarea class="pngx-ajax-wp-editor ' . esc_attr( $class ) . '" id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $name ) . '" placeholder="' . esc_attr( $std ) . '" rows="' . absint( $rows ) . '" cols="' . absint( $cols ) . '">' . format_for_editor( $value ) . '</textarea>';
			}

		} else {

			$wysiwyg_options                  = isset( $field['options'] ) ? $field['options'] : array();
			$wysiwyg_options['textarea_name'] = $name;
			$wysiwyg_options['editor_class']  = $name;

			wp_editor( $value, $field['id'], $wysiwyg_options );
		}

		if ( "" != $field['desc'] ) {
			echo '<br /><span class="description">' . $field['desc'] . '</span>';
		}

	}

}
