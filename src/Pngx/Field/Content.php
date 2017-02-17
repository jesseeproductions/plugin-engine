<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Field__Content' ) ) {
	return;
}


/**
 * Class Pngx__Field__Content
 * Title
 */
class Pngx__Field__Content {

	public static function display( $field = array(), $post_id = null, $meta = null, $template_fields = array(), $var = array() ) {

		$class = $field['display']['class'] ? $field['display']['class'] : '';
		$style = Pngx__Style__Linked::get_styles( $field, $post_id );
		$tags  = isset( $field['display']['tags'] ) ? $field['display']['tags'] : 'title';

		//Apply all the_content filters manually
		$meta = wptexturize( $meta );
		$meta = convert_smilies( $meta );

		//WPAutop
		if ( cctor_options( 'cctor_wpautop', true, 1 ) != 1 ) {
			$meta = wpautop( $meta );
		}
		$meta = shortcode_unautop( $meta );
		$meta = prepend_attachment( $meta );
		//Run Shortcodes
		$meta = do_shortcode( $meta );

		?>
		<div class="pngx-content <?php echo esc_attr( $class ); ?>" <?php echo sanitize_textarea_field( $style ); ?>>
			<?php echo strip_tags( $meta, Pngx__Allowed_Tags::$tags() ); ?>
		</div>
		<?php

	}

}
