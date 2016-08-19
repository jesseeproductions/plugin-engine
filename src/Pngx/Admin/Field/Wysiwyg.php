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

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		$wysiwyg_options = isset( $field['options'] ) ? $field['options'] : array();
		$wysiwyg_options['textarea_name'] = $name;
		$wysiwyg_options['editor_class'] = $name;

		//self::cctor_pro_column_row_select();

		wp_editor( $value, $field['id'], $wysiwyg_options );

		if ( "" != $field['desc'] ) {
			echo '<br /><span class="description">' . $field['desc'] . '</span>';
		}

		//echo '<p>Use the display shortcodes to show coupon content when using the shortcode: [showhook] or the print view: [showprint].  <a href="#" onclick="return showHelp(\'cctor-display-shortcode\');">Click to see how.</a></p><p style="text-align:center;"><img id="cctor-display-shortcode" class="cctor-help-images" src="' . CCTOR_PRO_URL . 'admin/images/display-shortcode.gif"/></p>';



	}

}
