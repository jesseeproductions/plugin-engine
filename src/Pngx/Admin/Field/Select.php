<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Select' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Select
 * Select Field
 */
class Pngx__Admin__Field__Select {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $repeat_obj = null ) {

		global $pagenow;
		$selected = '';

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name     = $options_id;
			$selected = $options[ $field['id'] ] ? $options[ $field['id'] ] : $field['std'];
		} else {
			$name = $field['id'];

			//Set Meta Default
			if ( $meta ) {
				$selected = $meta;
			} elseif ( $pagenow == 'post-new.php' && isset( $field['value'] ) ) {
				$selected = $field['value'];
			}
		}


		if ( $meta ) {
			$selected = $meta;
		} elseif ( $pagenow == 'post-new.php' && isset( $field['value'] ) ) {
			$selected = $field['value'];
		}

		$class     = isset( $field['class'] ) ? $field['class'] : '';

		if ( $repeat_obj ) {
			//log_me( 'repeat meta select' );
			//log_me( $repeat_obj );
			//$name = 'wpe_menu_section[' . $name . $repeat_obj->get_current_sec_col() . ']';
			$name = $name = $repeat_obj->get_field_name( $name );
			//$repeating = $repeat_obj->get_current_sec_col() . '[]';
		}

		?>

		<select
				id="<?php echo esc_attr( $field['id'] ); ?>"
				class="select <?php echo esc_attr( $class ); ?>"
				name="<?php echo esc_attr( $name ); ?>"
			<?php echo isset( $field['data'] ) ? Pngx__Admin__Fields::toggle( $field['data'], null ) : ''; ?>
		>
			<?php
			foreach ( $field['choices'] as $value => $label ) {

				$style = isset( $field['class'] ) && 'css-select' == $field['class'] ? 'style="' . esc_attr( $value ) . '"' : '';

				echo '<option ' . $style . ' value="' . esc_attr( $value ) . '"' . selected( $selected, $value, false ) . '>' . esc_attr( $label ) . '</option>';

			}
			?>
		</select>

		<?php
	}

}
