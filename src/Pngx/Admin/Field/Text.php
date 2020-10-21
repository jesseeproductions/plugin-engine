<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Text' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Text
 * Text Field
 */
class Pngx__Admin__Field__Text {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $var = null ) {

		if ( ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
			if ( ! $value && isset( $field['value'] ) ) {
				$value = $field['value'];
			}
		}

		$size      = isset( $field['size'] ) ? $field['size'] : 30;
		$class     = isset( $field['class'] ) ? $field['class'] : '';
		$std       = isset( $field['std'] ) ? $field['std'] : '';
		$condition = isset( $field['condition'] ) ? $field['condition'] : '';
		$attributes = empty( $field['field_attributes'] ) ? '' : Pngx__Admin__Field_Methods::instance()->set_field_attributes( $field['field_attributes'] );
		$bumpdown   = empty( $field['bumpdown'] ) ? '' : Pngx__Admin__Field_Methods::instance()->set_bumpdown( $field['bumpdown'] );

		if ( ! empty( $var['name'] ) ) {
			$name = $var['name'];
		}

		if ( isset( $field['alert'] ) && '' != $field['alert'] && 1 == $condition ) {
			echo '<div class="pngx-error">&nbsp;&nbsp;' . esc_html( $field['alert'] ) . '</div>';
		}

		echo '<input 
				type="text" 
				id="' . esc_attr( $field['id'] ) . '" 
				class="regular-text ' . esc_attr( $class ) . '"  
				name="' . esc_attr( $name ) . '" 
				placeholder="' . esc_attr( $std ) . '" 
				value="' . esc_attr( $value ) . '" 
				size="' . absint( $size ) . '" 
				' . $attributes . '
				' . ( ! empty( $field['post_title'] ) ? 'data-post-title="' . esc_attr( $field['post_title'] ) . '"' : '' ) .
			'/>';

		echo $bumpdown;

		if ( isset( $field['desc'] ) && ! empty( $field['desc'] ) ) {
			echo '<span class="description">' . esc_html( $field['desc'] ) . '</span>';
		}

	}

}
