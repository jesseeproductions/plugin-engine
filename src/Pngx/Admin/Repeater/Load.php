<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 *
 *
 *
 */
class Pngx__Repeater__Load {

	public function cycle_repeaters() {

		// loop
		foreach ( $this->meta as $field_key => $value ) {

			// bail early if not found
			if ( ! $this->repeater_fields[ $field_key ] ) {
				log_me( 'bail no field' );
				continue;
			}

			// get field
			$field = $this->repeater_fields[ $field_key ];
			$input = $field_key;
			//$input = '$this->meta[' . $field_key . ']';


			// validate
			$this->load_admin( $value, $field, $input );

		}

	}

	function load_value( $value, $post_id, $field ) {

		// bail early if no value
		if( empty($value) ) return false;


		// bail ealry if not numeric
		if( !is_numeric($value) ) return false;


		// bail early if no sub fields
		if( empty($field['sub_fields']) ) return false;


		// vars
		$value = intval($value);
		$rows = array();


		// loop
		for( $i = 0; $i < $value; $i++ ) {

			// create empty array
			$rows[ $i ] = array();


			// loop through sub fields
			foreach( array_keys($field['sub_fields']) as $j ) {

				// get sub field
				$sub_field = $field['sub_fields'][ $j ];


				// bail ealry if no name (tab)
				if( acf_is_empty($sub_field['name']) ) continue;


				// update $sub_field name
				$sub_field['name'] = "{$field['name']}_{$i}_{$sub_field['name']}";


				// get value
				$sub_value = acf_get_value( $post_id, $sub_field );


				// add value
				$rows[ $i ][ $sub_field['key'] ] = $sub_value;

			}

		}


		// return
		echo "{$input}[{$i}][{$k}]<br>";
		//return $rows;

	}

	public function load_admin( $value, $field, $input ) {

		// check sub fields
		if ( ! empty( $field['repeater_fields'] ) && ! empty( $value ) ) {

			$keys = array_keys( $value );

			foreach ( $keys as $i ) {

				foreach ( $field['repeater_fields'] as $sub_field ) {

					// vars
					$k = $sub_field['id'];

					// test sub field exists
					if ( ! isset( $value[ $i ][ $k ] ) ) {
						continue;

					}

					if ( 'repeater' === $sub_field['type'] ) {
						$val = count( $value[ $i ][ $k ] );
					} else {
						$val = $value[ $i ][ $k ];
					}


					echo "{$input}[{$i}][{$k}]<br>";

				}

			}

		}

		//return $valid;

	}

}