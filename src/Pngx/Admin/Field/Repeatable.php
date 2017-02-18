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

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $repeat_obj = null ) {

		if ( ! isset( $field['repeatable_fields'] ) || ! is_array( $field['repeatable_fields'] ) ) {
			return;
		}

		global $post;

		if ( ! $repeat_obj ) {
			$repeat_obj = new Pngx__Admin__Repeater__Main( $field['id'], (int)$meta );
		}

		//log_me( 'repeat meta' );
		//log_me( count( $meta ) );
		//log_me( $repeat_obj );
		//log_me( $meta );

		$class = isset( $field['class'] ) ? $field['class'] : '';

		$options[] = array(
			'wrap'  => 'li',
			'class' => 'repeatable-item',
		);

		$repeating_type = '';
		if ( $repeat_obj->get_repeating_sections_status() ) {
			$repeating_type = 'section';
		} elseif ( $repeat_obj->get_repeating_columns_status() ) {
			$repeating_type = 'column';
		}

		?>
		<ul
				id="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
				class="pngx-repeatable <?php echo esc_attr( $class ); ?>"
				data-clone="<?php echo esc_attr( json_encode( $options ) ); ?>"
				data-ajax_field_id="<?php echo esc_attr( $field['id'] ); ?>"
				data-ajax_action="pngx_repeatable"
				data-repeat-type="<?php echo esc_attr( $repeating_type ); ?>"
		>

			<?php


			$count = $repeat_obj->get_total_sections();
			if ( $repeat_obj->get_repeating_columns_status() ) {
				$count = $repeat_obj->get_total_columns();
			}

			for ( $i = 0; $i < $count; $i ++ ) {

				$section = get_post_meta( $post->ID, $repeat_obj->get_meta_id(), true );

				if ( ! is_array( $section ) ) {
					self::display_repeat_fields( $field['repeatable_fields'], $field, null, $repeat_obj, $meta );
				}
				//log_me($section);
				foreach( $section as $row ) {
				//	log_me($row);
					self::display_repeat_fields( $field['repeatable_fields'], $field, $row, $repeat_obj, $meta );
				}

				if ( $repeat_obj->get_repeating_sections_status() ) {
					$repeat_obj->update_section_count();
				} elseif ( $repeat_obj->get_repeating_columns_status() ) {
					$repeat_obj->update_column_count();
				}

			}

			?>
		</ul>

		<?php

	}

	public static function display_repeat_fields( $fields = array(), $parent = array(), $row = null, $repeat_obj, $meta = null ) {

		if ( ! is_object( $repeat_obj ) ) {
			return;
		}
		?>
		<li class="repeatable-item repeatable-item-<?php echo esc_attr( $repeat_obj->get_current_sec_col() ); ?>">

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


						Pngx__Admin__Fields::display_field( $repeater, false, false, $repeat_field_val, $repeat_obj );

						?>

					</div>

				</div>
				<?php

			}
			?>
			<a class="add-repeatable button"
			   data-repeater="<?php echo esc_attr( $parent['id'] ); ?>-repeatable"
			   data-section="<?php echo absint( $repeat_obj->get_current_section() ); ?>"
			   data-column="<?php echo esc_attr( $repeat_obj->get_current_column() ); ?>"
			   href="#"
			>+</a>
			<a class="remove-repeatable button"
			   data-repeater="<?php echo esc_attr( $parent['id'] ); ?>-repeatable"
			   data-section="<?php echo absint( $repeat_obj->get_current_section() ); ?>"
			   data-column="<?php echo esc_attr( $repeat_obj->get_current_column() ); ?>"
			   href="#"
			>X</a>
		</li>
		<?php
	}

}
