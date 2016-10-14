<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Image' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Text
 * Image Field
 */
class Pngx__Admin__Field__Image {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}
		$imagemsg = isset( $field['imagemsg'] ) ? $field['imagemsg'] : '';
		$class    = isset( $field['class'] ) ? $field['class'] : '';
		$imagesrc = '';

		if ( is_numeric( $value ) ) {
			$imagesrc     = wp_get_attachment_image_src( absint( $value ), 'medium' );
			$imagesrc     = $imagesrc[0];
			$imagedisplay = '<div style="display:none" id="' . esc_attr( $field['id'] ) . '" class="pngx-default-image pngx-image-wrap">' . esc_html( $imagemsg ) . '</div> <img src="' . $imagesrc . '" id="' . esc_attr( $field['id'] ) . '" class="pngx-image pngx-image-wrap-img" />';
		} else {
			$imagedisplay = '<div style="display:block" id="' . esc_attr( $field['id'] ) . '" class="pngx-default-image pngx-image-wrap">' . esc_html( $imagemsg ) . '</div> <img style="display:none" src="' . $imagesrc . '" id="' . esc_attr( $field['id'] ) . '" class="pngx-image pngx-image-wrap-img" />';
		}

		echo $imagedisplay . '<br>';

		echo '<input class="pngx-upload-image ' . esc_attr( $class ) . '"  type="hidden" id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />';
		echo '<button id="' . esc_attr( $field['id'] ) . '" class="pngx-image-button">Upload Image</button>';
		echo '<small> <a href="#" id="' . esc_attr( $field['id'] ) . '" class="pngx-clear-image">Remove Image</a></small>';

		if ( "" != $field['desc'] ) {
			echo '<br /><span class="description">' . $field['desc'] . '</span>';
		}

	}

}