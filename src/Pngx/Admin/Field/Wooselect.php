<?php
/**
 * Handles the WooSelect Dropdown fields.
 *
 * @since   4.0.0
 *
 * @package Pngx\Admin\Field
 */

namespace Pngx\Admin\Field;

/**
 * Class Pngx__Admin__Field__Wooselect
 *
 * @since 4.0.0
 */
class Wooselect {

	public static function display( $field = [], $option_value = [], $options_id = null, $meta = null, $var = null, $template = null ) {
		global $pagenow;
		$selected = '';

		if ( ! empty( $var['repeating'] ) && ! empty( $var['value'] ) ) {
			$meta = $var['value'];
		}
		$name = '';
		if ( ! empty( $options_id ) ) {
			$name     = $options_id;
			$selected = $option_value[ $field['id'] ] ? $option_value[ $field['id'] ] : $field['std'];
		} else {
			$name = $field['id'];
		}
		if ( ! empty( $field['attrs']['multiple'] ) ) {
			$name .= '[]';
		}

		if ( ! empty( $var['name'] ) ) {
			$field['name'] = $var['name'];
		}

		if ( $meta ) {
			$selected = $meta;
		} elseif ( $pagenow == 'post-new.php' && isset( $field['value'] ) ) {
			$selected = $field['value'];
		}

		$classes[] = ! empty( $field['display']['class'] ) ? $field['display']['class'] : '';

		$selected_option = [];
		if ( ! empty( $field['attrs']['data-source'] ) ) {
			$selected_text   = empty( $selected ) ? '' : get_the_title( $selected );
			$selected_option = [
				[
					'id'   => $selected,
					'text' => $selected_text,
				]
			];
		}

		if ( is_array( $field['options'] ) ) {
			foreach ( $field['options'] as $key => $option ) {
				if ( is_array( $selected ) ) {
					$field['options'][ $key ]['selected'] = in_array( $option['id'], $selected, true );
				} else {
					$field['options'][ $key ]['selected'] = $option['id'] === $selected ? true : false;
				}
			}
		}

		$selected =  is_array( $selected ) ? implode( ',', $selected ) : $selected;

		$template->template( 'components/field', [
				'classes_wrap'  => [ "pngx-engine-field__{$field['id']}-wrap", ...$field['fieldset_wrap'] ],
				'id'            => $field['id'],
				'label'         => $field['label'],
				'tooltip'       => $field['tooltip'] ?? null,
				'template_name' => 'dropdown',
				'template_echo' => true,
				'template_args' => [
					'label'           => $field['label'],
					'id'              => $field['id'],
					'classes_label'   => [ 'screen-reader-text' ],
					'classes_select'  => $classes,
					'name'            => $name,
					'selected'        => $selected,
					'selected_option' => $selected_option,
					'attrs'           => [
						...$field['attrs'],
						'data-selected' => $selected,
						'data-options'  => json_encode( $field['options'] ),
					],
					'wrap_attrs'      => empty( $field['wrap_attrs'] ) ? [] : $field['wrap_attrs'],
				],
			]
		);
	}
}
