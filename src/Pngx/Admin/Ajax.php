<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Class Pngx__Admin__Ajax
 *
 */
class Pngx__Admin__Ajax {


	public function __construct() {

		$this->start();

	}

	protected function start() {

		add_action( 'wp_ajax_pngx_templates', [ $this, 'load_templates' ] );

		add_action( 'wp_ajax_pngx_variety', [ $this, 'load_variety' ] );

	}

	public function load_templates() {

		// End if not the correct action
		if ( ! isset( $_POST['action'] ) || 'pngx_templates' !== $_POST['action'] ) {
			wp_send_json_error( __( 'A Permission Error has occurred. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		// End if not correct nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pngx_admin_' . $_POST['post_id'] ) ) {
			wp_send_json_error( __( 'Permission Error has occurred. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		if ( ! isset( $_POST['option'] ) ) {
			wp_send_json_error( __( 'No Template ID. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		Pngx__Main::instance()->doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		ob_start();

		/**
		 * Filter to Add All Fields for a Plugin
		 */
		$fields = apply_filters( 'pngx_meta_fields', array() );

        global $wp_version;

		foreach ( $fields as $field ) {

			$field_template = isset( $field['template'] ) ? $field['template'] : array();

			if ( $field['type'] && in_array( $_POST['option'], $field_template ) ) {

				// get value of this field if it exists for this post
				$meta = get_post_meta( $_POST['post_id'], $field['id'], true );

				//Wrap Class for Conditionals
				$wrapclass = isset( $field['wrapclass'] ) ? $field['wrapclass'] : '';

				?>

				<div class="pngx-meta-field-wrap field-wrap-<?php echo esc_html( $field['type'] ); ?> field-wrap-<?php echo esc_html( $field['id'] ); ?> <?php echo esc_html( $wrapclass ); ?>"
					<?php echo isset( $field['toggle'] ) ? Pngx__Admin__Fields::toggle( $field['toggle'], $field['id'] ) : null; ?> >

					<?php if ( isset( $field['label'] ) ) { ?>

						<div class="pngx-meta-label label-<?php echo $field['type']; ?> label-<?php echo $field['id']; ?>">
							<label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
						</div>

					<?php } ?>

					<div class="pngx-meta-field field-<?php echo $field['type']; ?> field-<?php echo $field['id']; ?>">

						<?php

						Pngx__Admin__Fields::display_field( $field, false, false, $meta, $wp_version );

						// Display admin linked style fields
						Pngx__Admin__Style__Linked::display_styles( $fields, $field, $_POST['post_id'] );

						?>

					</div>
					<!-- end .pngx-meta-field.field-<?php echo $field['type']; ?>.field-<?php echo $field['id']; ?> -->

				</div> <!-- end .pngx-meta-field-wrap.field-wrap-<?php echo $field['type']; ?>.field-wrap-<?php echo $field['id']; ?>	-->

				<?php
			}
		} // end foreach fields

		$template_fields = ob_get_contents();

		ob_end_clean();

		wp_send_json_success( json_encode( $template_fields ) );
	}

	public function load_variety() {

		//End if not the correct action
		if ( ! isset( $_POST['action'] ) || 'pngx_variety' != $_POST['action'] ) {
			wp_send_json_error( __( 'Permission Error has occurred. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		//End if not correct nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pngx_admin_' . $_POST['post_id'] ) ) {
			wp_send_json_error( __( 'Permission Error has occurred. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		if ( ! isset( $_POST['field'] ) ) {
			wp_send_json_error( __( 'No Field ID. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		if ( ! isset( $_POST['option'] ) ) {
			wp_send_json_error( __( 'No Option ID. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		Pngx__Main::instance()->doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		ob_start();

		/**
		 * Filter to Add All Fields for a Plugin
		 */
		$fields = apply_filters( 'pngx_meta_fields', array() );

		if ( isset( $fields[ $_POST['field'] ]['variety_choices'][ $_POST['option'] ] ) ) {
			foreach ( $fields[ $_POST['field'] ]['variety_choices'][ $_POST['option'] ] as $label ) {

				if ( ! isset( $fields[ $label ] ) ) {
					continue;
				}
				$meta = '';
				if ( isset( $_POST['post_id'] ) ) {
					$meta = get_post_meta( $_POST['post_id'], $label, true );
				}
				Pngx__Admin__Fields::display_field( $fields[ $label ], false, false, $meta, null );

			}
		}

		$field = ob_get_contents();

		ob_end_clean();

		wp_send_json_success( json_encode( $field ) );
	}

}