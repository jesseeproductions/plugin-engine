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

		global $post;

		$settings = array();
		if ( ! class_exists( '_WP_Editors' ) ) {
			require( ABSPATH . WPINC . '/class-wp-editor.php' );
		}
		$set = _WP_Editors::parse_settings( 'apid', $settings );
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

		_WP_Editors::editor_settings( esc_attr( $field['id'] ), $set );

		$functions = array(
			array(
				'addButton'    => 'showhook',
				'title'        => 'Add Show Hook Shortcode',
				'text'         => '[showhook]',
				'icon'         => false,
				'type'         => 'wrap',
				'wrapopentag'  => '[showhook]',
				'wrapclosetag' => '[/showhook]',
			),
			array(
				'addButton'    => 'showprint',
				'title'        => 'Add Print Hook Shortcode',
				'text'         => '[showprint]',
				'icon'         => false,
				'type'         => 'wrap',
				'wrapopentag'  => '[showprint]',
				'wrapclosetag' => '[/showprint]',
			),
		);

		$pngx_editor_vars = array(
			'url'            => get_home_url(),
			'includes_url'   => includes_url(),
			'editor_buttons' => apply_filters( 'pngx_visual_editor_functions', $functions, $post ),
		);

		wp_localize_script( 'pngx-wp-editor', 'pngx_editor_vars', $pngx_editor_vars );


		//if ( Pngx__Main::instance()->doing_ajax ) {
		$rows  = isset( $field['rows'] ) ? $field['rows'] : 12;
		$cols  = isset( $field['cols'] ) ? $field['cols'] : 50;
		$class = isset( $field['class'] ) ? $field['class'] : '';

		if ( version_compare( $wp_version, '4.3', '<' ) ) {
			echo '<textarea 
						class="pngx-ajax-wp-editor ' . esc_attr( $class ) . '" 
						id="' . esc_attr( $field['id'] ) . '" 
						name="' . esc_attr( $name ) . '" 
						placeholder="' . esc_attr( $std ) . '" 
						rows="' . absint( $rows ) . '" 
						cols="' . absint( $cols ) . '"
						>' . wp_htmledit_pre( $value ) . '</textarea>';
		} else {
			echo '<textarea 
						class="pngx-ajax-wp-editor ' . esc_attr( $class ) . '" 
						id="' . esc_attr( $field['id'] ) . '" 
						name="' . esc_attr( $name ) . '" 
						placeholder="' . esc_attr( $std ) . '" 
						rows="' . absint( $rows ) . '" 
						cols="' . absint( $cols ) . '"
						>' . format_for_editor( $value ) . '</textarea>';
		}

		/*} else {

			$wysiwyg_options                  = isset( $field['options'] ) ? $field['options'] : array();
			$wysiwyg_options['textarea_name'] = $name;
			$wysiwyg_options['editor_class']  = $name;

			wp_editor( $value, $field['id'], $wysiwyg_options );
		}*/

		if ( "" != $field['desc'] ) {
			echo '<br /><span class="description">' . $field['desc'] . '</span>';
		}

	}

}
