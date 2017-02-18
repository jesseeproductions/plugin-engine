<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Repeatable' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Repeatable
 * Repeatable Field
 */
class Pngx__Admin__Field__Repeatable {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $wp_version = null ) {

		if ( ! isset( $field['repeatable_fields'] ) || ! is_array( $field['repeatable_fields'] ) ) {
			return;
		}

		global $post;


		log_me('repeat meta');
		log_me($meta);

		wp_localize_script( 'pngx-admin', 'pngx_admin_repeatable_ajax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
			'nonce'   => wp_create_nonce( 'pngx_admin_rep_' . $post->ID ),
			'post_id' => $post->ID
		) );

		$class     = isset( $field['class'] ) ? $field['class'] : '';

		$options[] = array(
			'wrap'  => 'li',
			'class' => 'repeatable-item',
		);

		?>
		<ul
				id="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
				class="pngx-repeatable <?php echo esc_attr( $class ); ?>"
				data-clone="<?php echo esc_attr( json_encode( $options ) ); ?>"
				data-ajax_field_id="<?php echo esc_attr( $field['id'] ); ?>"
				data-ajax_action="pngx_repeatable"
		>

			<?php
			if ( $meta ) {

				foreach ( $meta as $row ) {

					self::display_repeat_fields( $field['repeatable_fields'], $field, $row, false, $meta, $wp_version );

				}
			} else {

				self::display_repeat_fields( $field['repeatable_fields'], $field, null, false, $meta, $wp_version );

			}

			?>
		</ul>

		<?php
		if ( isset( $field['desc'] ) && "" != $field['desc'] ) {
			echo '<br /><span class="description">' . $field['desc'] . '</span>';
		}
	}

	public static function display_repeat_fields( $fields = array(), $parent = array(), $row = null, $meta = null, $wp_version ) {

        $counter = 0;
		?>
		<li class="repeatable-item repeatable-item-<?php echo absint( $counter ); ?>">

			<span class="sort hndle">|||</span>
			<?php

			foreach ( $fields as $repeater ) {

				$repeat_field_val = isset( $row[ $repeater['id'] ] ) ? $row[ $repeater['id'] ] : $row;
				?>
				<div
						class="field-wrap-repeatable field-wrap-<?php echo esc_html( $repeater['type'] ); ?>
					            field-wrap-repeatable-<?php echo esc_html( $repeater['id'] ); ?>"
					<?php echo isset( $repeater['toggle'] ) ? Pngx__Admin__Fields::toggle( $repeater['toggle'], $repeater['id'] ) : null; ?>
				>
					<?php if ( isset( $repeater['label'] ) ) { ?>

						<div class="pngx-meta-label label-<?php echo $repeater['type']; ?> label-<?php echo $repeater['id']; ?>">
							<label for="<?php echo $repeater['id']; ?>"><?php echo $repeater['label']; ?></label>
						</div>

					<?php } ?>

					<div class="pngx-meta-field field-<?php echo $repeater['type']; ?> field-<?php echo $repeater['id']; ?>">

						<?php
						Pngx__Admin__Fields::display_field( $repeater, false, false, $repeat_field_val, $wp_version );

						?>

					</div>

				</div>
				<?php
                $counter++;
			}
			?>
			<a class="add-repeatable button" data-repeater="<?php echo esc_attr( $parent['id'] ); ?>-repeatable" href="#">+</a>
			<a class="remove-repeatable button" data-repeater="<?php echo esc_attr( $parent['id'] ); ?>-repeatable" href="#">X</a>
		</li>
		<?php
	}

}
