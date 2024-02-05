<?php
/**
 * View: Common Textarea Input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/textarea.php
 *
 * See more documentation about our views templating system.
 *
 * @since 4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://pngx.ink/RYIOh
 *
 * @var array<string,string> $classes_wrap  An array of classes for the text wrap.
 * @var array<string,string> $classes_label An array of classes for the label.
 * @var array<string,string> $classes_input An array of classes for the text input.
 * @var string               $label         The label for the text input.
 * @var string               $id            ID of the text input.
 * @var string               $name          The name for the text input.
 * @var string               $placeholder   The placeholder for the text input.
 * @var array<string|mixed>  $page          The page data.
 * @var string               $value         The value of the text field.
 * @var number               $rows          The number of rows for the textarea.
 * @var number               $cols          The number of cols for the textarea.
 * @var array<string,string> $attrs         Associative array of attributes of the text input.
 * @var array<string,string> $wrap_attrs      Associative array of attributes of the field wrap.
 */

$wrap_classes = [ 'pngx-engine-options-control', 'pngx-engine-options-control__textarea-wrap' ];
if ( ! empty( $classes_wrap ) ) {
	$wrap_classes = array_merge( $wrap_classes, $classes_wrap );
}

$label_classes = [ 'pngx-engine-options-control__label' ];
if ( ! empty( $classes_label ) ) {
	$label_classes = array_merge( $label_classes, $classes_label );
}

$input_classes = [ 'pngx-engine-options-control__textarea-input' ];
if ( ! empty( $classes_input ) ) {
	$input_classes = array_merge( $input_classes, $classes_input );
}

// Keep value with no spaces before or after the php code to prevent spaces in the output.
?>
<div <?php pngx_classes( $wrap_classes ); ?> >
	<label
		<?php pngx_classes( $classes_label ); ?>
		for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label>
	<textarea
		id="<?php echo esc_attr( $id ); ?>"
		<?php pngx_classes( $input_classes ); ?>
		name="<?php echo esc_html( $name ); ?>"
		placeholder="<?php echo esc_attr( $placeholder ); ?>"
		rows="<?php echo absint( $rows ); ?>"
		cols="<?php echo absint( $cols ); ?>"
		<?php pngx_attributes( $attrs ) ?>
	><?php echo format_for_editor( $value ); ?></textarea>
	<?php
	//global $post, $wp_version;

	$settings = array();
	if ( ! class_exists( '_WP_Editors' ) ) {
		require( ABSPATH . WPINC . '/class-wp-editor.php' );
	}
	$set = _WP_Editors::parse_settings( esc_attr( $id ), $settings );
	if ( ! current_user_can( 'upload_files' ) ) {
		$set['media_buttons'] = false;
	}

	if ( $set['media_buttons'] ) {
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'media-upload' );
		$post = get_post();
		if ( ! $post && ! empty( $GLOBALS['post_ID'] ) ) {
			$post = $GLOBALS['post_ID'];
		}
		wp_enqueue_media( array(
			'post' => $post
		) );
	}

	wp_enqueue_script( 'tiny_mce' );

	_WP_Editors::editor_settings( esc_attr( $id ), $set );

	// Only Localize Script Once Per Page
	if ( ! isset( $post->load_scripts ) ) {

		/**
		 * Filter Tiny MCE Buttons for PNGX Editor Script
		 *
		 * @param array() an    array of attributes to create the button
		 * @param         $post current post object
		 */
		$pngx_visual_editor_buttons = apply_filters( 'pngx_visual_editor_functions', array(), $post );

		/**
		 * Filter HTML Editor Buttons for PNGX Editor Script
		 *
		 * @param array() an    array of attributes to create the button
		 * @param         $post current post object
		 */
		$pngx_html_editor_buttons = apply_filters( 'pngx_html_editor_functions', array(), $post );

		/**
		 * Variables for WP Editor Script
		 */
		$pngx_editor_vars = array(
			'url'                   => get_home_url(),
			'includes_url'          => includes_url(),
			'visual_editor_buttons' => $pngx_visual_editor_buttons,
			'html_editor_buttons'   => $pngx_html_editor_buttons,
			'rich_editing'          => get_user_meta( get_current_user_id(), 'rich_editing', true ),
		);
		wp_localize_script( 'pngx-wp-editor', 'pngx_editor_vars', $pngx_editor_vars );

		if ( is_object( $post ) ) {
			$post->load_scripts = true;
		}
	}

	$std       = isset( $field['std'] ) ? $field['std'] : '';
	$rows      = isset( $field['rows'] ) ? $field['rows'] : 12;
	$cols      = isset( $field['cols'] ) ? $field['cols'] : 50;
	$class     = isset( $field['class'] ) ? $field['class'] : '';
	$repeating = isset( $field['repeating'] ) ? '[]' : '';

	?>

	<textarea
		class="pngx-ajax-wp-editor <?php echo esc_attr( $class ); ?>"
		id="<?php echo esc_attr( $field['id'] ); ?>"
		name="<?php echo esc_attr( $name ) . $repeating; ?>"
		placeholder="<?php echo esc_attr( $std ); ?>"
		rows="<?php echo absint( $rows ); ?>"
		cols="<?php echo absint( $cols ); ?>"
			<?php echo isset( $field['data'] ) ? Pngx__Admin__Fields::toggle( $field['data'], null ) : ''; ?>
        >
        	<?php echo format_for_editor( $value ); ?>
		</textarea>
	<?php
	/*		if ( isset( $field['desc'] ) && ! empty( $field['desc'] ) ) {
				echo '<span class="description">' . esc_html( $field['desc'] ) . '</span>';
			}*/
	?>
</div>