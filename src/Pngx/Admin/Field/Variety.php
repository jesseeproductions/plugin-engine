<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Variety' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Variety
 * Chooser Field for a variety of fields
 */
class Pngx__Admin__Field__Variety {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		global $pagenow;
		$selected = '';

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name     = $options_id;
			$selected = $options[ $field['id'] ] ? $options[ $field['id'] ] : $field['std'];
		} else {
			$name = $field['id'];

			// Set Meta Default
			if ( $meta ) {
				$selected = $meta;
			} elseif ( 'post-new.php' === $pagenow && isset( $field['value'] ) ) {
				$selected = $field['value'];
			} elseif ( Pngx__Main::instance()->doing_ajax ) {
				$selected = $field['value'];
			}


			if ( $meta ) {
				$selected = $meta;
			} elseif ( 'post-new.php' === $pagenow && isset( $field['value'] ) ) {
				$selected = $field['value'];
			} elseif ( Pngx__Main::instance()->doing_ajax ) {
				$selected = $field['value'];
			}

			$class     = isset( $field['class'] ) ? $field['class'] : '';
			$repeating = isset( $field['repeating'] ) ? '[]' : '';

			?>
			<div class="pngx-one-third pngx-first">
				<select
						id="<?php echo esc_attr( $field['id'] ); ?>"
						class="select pngx-variety-select <?php echo esc_attr( $class ); ?>"
						name="<?php echo esc_attr( $name ) . $repeating; ?>"
					<?php echo isset( $field['data'] ) ? Pngx__Admin__Fields::toggle( $field['data'], null ) : ''; ?>
				>
					<?php
					foreach ( $field['choices'] as $value => $label ) {

						$style = isset( $field['class'] ) && 'css-select' === $field['class'] ? 'style="' . esc_attr( $value ) . '"' : '';

						echo '<option ' . $style . ' value="' . esc_attr( $value ) . '"' . selected( $selected, $value, false ) . '>' . esc_attr( $label ) . '</option>';

					}
					?>
				</select>
			</div>
			<div class="pngx-two-thirds">
				<?php

				/**
				 * Filter Template Fields to get infomation to display in the Variety Field
				 */
				$fields = apply_filters( 'pngx_meta_template_fields', array() );

				global $post;

				if ( isset( $field['variety_choices'][ $selected ] ) ) {
					foreach ( $field['variety_choices'][ $selected ] as $label ) {

						if ( ! isset( $fields[ $label ] ) ) {
							continue;
						}

						$post_id = $post->ID;
						if ( Pngx__Main::instance()->doing_ajax && isset( $_POST['post_id'] ) ) {
							$post_id = absint( $_POST['post_id'] );
						}

						$meta = get_post_meta( $post_id, $label, true );
						Pngx__Admin__Fields::display_field( $fields[ $label ], false, false, $meta, null );

					}
				}
				?>

			</div>
			<?php
			if ( '' !== $field['desc'] ) {
				echo '<br /><span class="description">' . esc_html( $field['desc'] ) . '</span>';
			}

		}

	}
}
