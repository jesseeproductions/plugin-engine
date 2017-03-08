<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Plugin Engine Admin Repeater Class
 *
 *
 */
class Pngx__Repeater__Main {

	protected $id;
	protected $meta;
	protected $post_id;
	protected $info;
	protected $repeater_fields;

	/**
	 * Pngx__Repeater__Main constructor.
	 */
	public function __construct( $repeater_id, $meta, $post_id, $save = false ) {
		/**
		 * todo list
		 *
		 * dyanmically generate name and get value (might have to change price)
		 * save new values
		 * sanitze values
		 * delete rows that no longer exist
		 * display values on front end
		 * add in repeater system for dynamic fields
		 *
		 */
		$this->id                = $repeater_id;
		$this->post_id           = $post_id;
		$this->meta[ $this->id ] = is_array( $meta ) ? $meta : array();
		$this->info              = $this->get_post_id_info();
		$this->repeater_fields   = apply_filters( 'pngx_meta_repeater_fields', array() );
		$this->loader   = new Pngx__Repeater__Load();

		if ( $save ) {

			//$this->update_value( $this->meta , $this->repeater_fields[$this->id] );
			$this->cycle_repeaters();

		} else {

			//$this->loader->cycle_repeaters();

		}

	}

	public function cycle_repeaters() {


		// bail early if no $_POST
		if ( empty( $this->meta ) ) {
			return;
		}


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
			$this->validate_value( $value, $field, $input );

		}

	}

	public function validate_value( $value, $field, $input ) {

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


//					$input_name = "{$input}[{$i}][{$k}]";
//					$input_name = str_replace( "[", "_", $input_name );
//					$input_name = str_replace( "]", "_", $input_name );
					//update_metadata( $this->info['type'], $this->info['id'], $input_name, $val );

					$this->update_metadata( "{$input}[{$i}][{$k}]", $val );
					//update_metadata( $this->info['type'], $this->info['id'], "{$input}[{$i}][{$k}]", $val );

					$this->validate_value( $value[ $i ][ $k ], $sub_field, "{$input}[{$i}][{$k}]" );

				}

			}

		}

		//return $valid;

	}

	public function get_post_id_info() {

		// vars
		$info = array(
			'type' => 'post',
			'id'   => 0
		);

		// bail early if no $this->post_id
		if ( ! $this->post_id ) {
			return $info;
		}

		// numeric
		if ( is_numeric( $this->post_id ) ) {

			$info['id'] = (int) $this->post_id;

			// string
		} elseif ( is_string( $this->post_id ) ) {

			// vars
			$glue = '_';
			$type = explode( $glue, $this->post_id );
			$id   = array_pop( $type );
			$type = implode( $glue, $type );
			$meta = array( 'post', 'user', 'comment', 'term' ); // add in 'term'


			// meta
			if ( is_numeric( $id ) && in_array( $type, $meta ) ) {

				$info['type'] = $type;
				$info['id']   = (int) $id;

				// option
			} else {

				$info['type'] = 'option';
				$info['id']   = $this->post_id;

			}

		}

		// return
		return $info;

	}

	public function update_metadata( $name = '', $value = '', $hidden = false ) {

		// vars
		$return = false;
		$prefix = $hidden ? '_' : '';


		// get post_id info
		$info = $this->get_post_id_info();


		// bail early if no $post_id (acf_form - new_post)
		if ( ! $info['id'] ) {
			return $return;
		}


		$name   = $prefix . $name;
		$return = update_metadata( $info['type'], $info['id'], $name, $value );


		// return
		return $return;

	}























	public function acf_update_value( $value = null, $field ) {

		// strip slashes
		//if( acf_get_setting('stripslashes') ) {

		//$value = stripslashes_deep($value);

		//}

		// allow null to delete
		if ( $value === null ) {

			return $this->delete_value( $field );

		}


		// update value
		$return = $this->update_metadata( $field['id'], $value );


		// update reference
		$this->update_metadata( $field['id'], $field['id'], true );

		// return
		return $return;

	}


	public function update_value( $value, $field ) {

		// bail early if no sub fields
		if ( empty( $field['repeater_fields'] ) ) {
			return $value;
		}


		// vars
		$new_value = 0;
		$old_value = (int) $this->get_metadata( $field['id'] );


		// update sub fields
		if ( ! empty( $value ) ) {
			$i = - 1;


			// loop through rows
			foreach ( $value as $row ) {
				$i ++;

				// bail early if no row
				if ( ! is_array( $row ) ) {
					log_me( 'bail row is not array' );
					continue;
				}


				// update row
				$this->update_row( $row, $i, $field );


				// append
				$new_value ++;

			}

		}

		// remove old rows
		if ( $old_value > $new_value ) {

			// loop
			for ( $i = $new_value; $i < $old_value; $i ++ ) {

				$this->delete_row( $i, $field );

			}

		}

		// save false for empty value
		if ( empty( $new_value ) ) {
			$new_value = '';
		}

		// return
		return $new_value;
	}

	public function update_row( $row, $i = 0, $field ) {

		// bail early if no layout reference
		if ( ! is_array( $row ) ) {
			return false;
		}


		// bail early if no layout
		if ( empty( $field['repeater_fields'] ) ) {
			return false;
		}

		// loop
		foreach ( $field['repeater_fields'] as $repeater_fields ) {

			// value
			$value = null;


			// find value (id)
			if ( isset( $row[ $repeater_fields['id'] ] ) ) {

				$value = $row[ $repeater_fields['id'] ];

				// find value (name)
			} elseif ( isset( $row[ $repeater_fields['id'] ] ) ) {

				$value = $row[ $repeater_fields['id'] ];

				// value does not exist
			} else {

				continue;

			}


			// modify name for save
			$repeater_fields['id'] = "{$field['id']}_{$i}_{$repeater_fields['id']}";


			// update field
			$this->acf_update_value( $value, $repeater_fields );

		}


		// return
		return true;

	}


	public function get_metadata( $name = '', $hidden = false ) {

		// vars
		$value  = null;
		$prefix = $hidden ? '_' : '';


		// get post_id info
		$info = $this->get_post_id_info();


		// bail early if no $post_id (acf_form - new_post)
		if ( ! $info['id'] ) {
			return $value;
		}


		// option
		if ( $info['type'] === 'option' ) {

			$name  = $prefix . $this->post_id . '_' . $name;
			$value = get_option( $name, null );

			// meta
		} else {

			$name = $prefix . $name;
			$meta = get_metadata( $info['type'], $info['id'], $name, false );

			if ( isset( $meta[0] ) ) {

				$value = $meta[0];

			}

		}


		// return
		return $value;

	}





	function delete_row( $i = 0, $field ) {

		// bail early if no sub fields
		if ( empty( $field['repeater_fields'] ) ) {
			return false;
		}


		// loop
		foreach ( $field['repeater_fields'] as $repeater_fields ) {

			// modify name for delete
			$repeater_fields['id'] = "{$field['id']}_{$i}_{$repeater_fields['id']}";


			// delete value
			$this->delete_value( $repeater_fields );

		}


		// return
		return true;

	}

	public function delete_value( $field ) {

		// delete value
		$return = $this->delete_metadata( $field['id'] );


		// delete reference
		$this->delete_metadata( $field['id'], true );

		// return
		return $return;

	}

	public function delete_metadata( $name = '', $hidden = false ) {

		// vars
		$return = false;
		$prefix = $hidden ? '_' : '';


		// get post_id info
		$info = $this->get_post_id_info();


		// bail early if no $post_id (acf_form - new_post)
		if ( ! $info['id'] ) {
			return $return;
		}


		$name   = $prefix . $name;
		$return = delete_metadata( $info['type'], $info['id'], $name );


		// return
		return $return;

	}
}