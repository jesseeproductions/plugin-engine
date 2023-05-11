<?php
/**
 * Handles the Switch Field.
 *
 * @since   4.0.0
 *
 * @package Pngx\Admin\Field
 */

namespace Pngx\Admin\Field;

/**
 * Class Switch_Field
 *
 * @since 4.0.0
 */
class Switch_Field {

	public static function display( $field = [], $option_value = [], $options_id = null, $meta = null, $var = null, $template = null ) {
		global $pagenow;
		$selected = '';

		if ( ! empty( $options_id ) ) {
			$name     = $options_id;
			$selected = $option_value[ $field['id'] ];
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

		$classes     = (array) isset( $field['class'] ) ? $field['class'] : [];
		$std       = isset( $field['std'] ) ? $field['std'] : '';

		$template->template( 'components/field', [
				'classes_wrap'  => [ "pngx-engine-field__{$field['id']}-wrap", ...$field['fieldset_wrap'] ],
				'id'            => $field['id'],
				'label'         => $field['label'],
				'tooltip'       => $field['tooltip'] ?? null,
				'template_name' => 'switch',
				'template_echo' => true,
				'template_args' => [
					'id'            => $field['id'],
					'label'         => $field['label'],
					'classes_wrap'  => [],
					'classes_input' => $classes,
					'classes_label' => [],
					'name'          => $name,
					'value'         => 1,
					'checked'       => $selected,
					'attrs'         => $field['attrs'],
					'wrap_attrs'      => empty( $field['wrap_attrs'] ) ? [] : $field['wrap_attrs'],
				],
			]
		);
	}
}
