<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__File' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Text
 * File Field
 */
class Pngx__Admin__Field__File {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $repeat_obj = null ) {

		global $pagenow;

		if ( ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		if ( 'post-new.php' == $pagenow && ! $value && isset( $field['std'] ) ) {
			$value = $field['std'];
		}

		$class     = isset( $field['class'] ) ? $field['class'] : '';
		$repeating = isset( $field['repeating'] ) ? '[]' : '';
		$filedisplay = '<div class="' . esc_attr( $field["id"] ) . ' pngx-file-url" data-default-msg="Upload a CSV file to register users from" data-prefix="CSV File: ">' . 'Upload a CSV file to register users from' . '</div>';
		$register_attendees = '';

		if ( is_numeric( $value ) ) {
			$filesrc     = wp_get_attachment_url( absint( $value ) );
			$filesrc = wp_normalize_path( $filesrc );
			$filedisplay = '<div class="' . esc_attr( $field["id"] ) . ' pngx-file-url" data-default-msg="Upload a CSV file to register users from" data-prefix="CSV File: ">CSV File: ' .  $filesrc . '</div>';

			$register_attendees = get_submit_button(
				esc_attr__( 'Register Attendees', 'event-connection' ),
				'secondary',
				'ecngx-csv-register-attendees',
				true
			);
		}

		echo $filedisplay . '<br>';

		?>

		<input
				class="pngx-upload-file <?php echo esc_attr( $class ); ?>"
				type="hidden" id="<?php echo esc_attr( $field['id'] ); ?>"
				name="<?php echo esc_attr( $name ) . $repeating; ?>"
				value="<?php echo esc_attr( $value ); ?>"
		/>

		<button id="<?php echo esc_attr( $field['id'] ); ?>" class="pngx-file-button" <?php echo isset( $field['function'] ) ? Pngx__Admin__Fields::toggle( $field['function'], $field['id'] ) : null; ?> >Upload File</button>

		<small><a href="#" id="<?php echo esc_attr( $field['id'] ); ?>" class="pngx-clear-file">Remove File</a></small>

		<?php
		if ( isset( $field['desc'] ) && ! empty( $field['desc'] ) ) {
			echo '<span class="description">' . esc_html( $field['desc'] ) . '</span>';
		}
		?>
		<div class="<?php echo esc_attr( $field['id'] ); ?> pngx-file-action">
			<?php
			echo $register_attendees;
			?>
		</div>
		<?php

	}

}
