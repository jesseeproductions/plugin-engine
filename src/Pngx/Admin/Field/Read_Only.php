<?php
/**
 * Handles the Read Only Fields.
 *
 * @since   4.0.0
 *
 * @package Pngx\Admin\Field
 */

namespace Pngx\Admin\Field;

/**
 * Class Read_Only
 *
 * @since 4.0.0
 */
class Read_Only {

	public static function display( $field = [], $options = [], $options_id = null, $meta = null, $var = null, $template = null ) {
		$name  = $field['id'];
		$value = $meta;
		if ( ! $value && isset( $field['value'] ) ) {
			$value = $field['value'];
		}

		if ( ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		}

		$template->template( 'components/read-only', [
			'classes_wrap'  => [ "pngx-engine-field__{$field['id']}-wrap" ],
			'label'         => $field['label'],
			'tooltip'       => $field['tooltip'] ?? null,
			'screen_reader' => $field['label'],
			'id'            => $field['id'],
			'name'          => $name,
			'value'         => $value,
		] );
	}
}
