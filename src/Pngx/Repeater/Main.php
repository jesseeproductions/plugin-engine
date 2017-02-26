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
	protected $repeater_fields;

	/**
	 * Pngx__Repeater__Main constructor.
	 */
	public function __construct( $repeater_id, $meta, $post_id, $save = false, $current_section = 0, $current_column = 0 ) {

		$this->id              = $repeater_id;
		$this->post_id         = $post_id;
		$this->meta            = is_array( $meta ) ? $meta : array();
		$this->repeater_fields = apply_filters( 'pngx_meta_repeater_fields', array() );

		if ( $save ) {


		}

	}


	//public function update_value( $value, $this->post_id, $field ) {
	public function update_value( $value, $field ) {
		log_me( 'update_value' );
		log_me( $value );
		log_me( $field );

		// vars
		$total = 0;


		// bail early if no sub fields
		if ( empty( $field['repeater_fields'] ) ) {
			return $value;
		}


		// update sub fields
		if ( ! empty( $value ) ) {

			// $i
			$i = - 1;


			// loop through rows
			foreach ( $value as $row ) {

				// $i
				$i ++;


				// increase total
				$total ++;


				// loop through sub fields
				foreach ( $field['repeater_fields'] as $sub_field ) {

					// value
					$v = false;


					// key (backend)
					///if ( isset( $row[ $sub_field['key'] ] ) ) {

					//$v = $row[ $sub_field['key'] ];

					//} elseif ( isset( $row[ $sub_field['name'] ] ) ) {
					if ( isset( $row[ $sub_field['id'] ] ) ) {

						$v = $row[ $sub_field['id'] ];

					} else {

						// input is not set (hidden by conditioanl logic)
						continue;

					}

					//log_me($field['name']);
					//log_me($i);
					///log_me($sub_field['name']);
					// modify name for save
					$sub_field['name'] = "{$field['name']}_{$i}_{$sub_field['name']}";


					// update value
					$this->update_values( $v, $sub_field );

				}
				// foreach

			}
			// foreach

		}
// if


		// get old value (db only)
		$old_total = (int) acf_get_metadata( $this->post_id, $field['name'] );

		if ( $old_total > $total ) {

			for ( $i = $total; $i < $old_total; $i ++ ) {

				foreach ( $field['repeater_fields'] as $sub_field ) {

					// modify name for delete
					$sub_field['name'] = "{$field['name']}_{$i}_{$sub_field['name']}";


					// delete value
					acf_delete_value( $this->post_id, $sub_field );

				}
				// foreach

			}
			// for

		}
// if


// update $value and return to allow for the normal save function to run
		$value = $total;


// save false for empty value
		if ( empty( $value ) ) {

			$value = '';

		}


// return
		return $value;
	}


//  function update_values( $value = null, $this->post_id = 0, $field ) {
	public function update_values( $value = null, $field ) {


		// update value
		$return = $this->update_metadata( $this->post_id, $field['name'], $value );


		// update reference
		$this->update_metadata( $this->post_id, $field['name'], $field['key'], true );


		// clear cache
		//acf_delete_cache("get_value/post_id={$this->post_id}/name={$field['name']}");
		//acf_delete_cache("format_value/post_id={$this->post_id}/name={$field['name']}");


		// return
		return $return;

	}

	//public function update_metadata( $this->post_id = 0, $name = '', $value = '', $hidden = false ) {
	public function update_metadata( $name = '', $value = '', $hidden = false ) {

		// vars
		$return = false;
		$prefix = $hidden ? '_' : '';


		// get post_id info
		$info = $this->get_post_id_info( $this->post_id );


		// bail early if no $this->post_id (acf_form - new_post)
		if ( ! $info['id'] ) {
			return $return;
		}


		// option
		/*if( $info['type'] === 'option' ) {

			$name = $prefix . $this->post_id . '_' . $name;
			$return = acf_update_option( $name, $value );

		// meta
		} else {*/

		$name   = $prefix . $name;
		$return = update_metadata( $info['type'], $info['id'], $name, $value );

		//}


		// return
		return $return;

	}

	public function delete_value( $field ) {

		// delete value
		$return = $this->delete_metadata( $this->post_id, $field['name'] );


		// delete reference
		$this->delete_metadata( $this->post_id, $field['name'], true );


		// clear cache
		//acf_delete_cache("get_value/post_id={$post_id}/name={$field['name']}");
		//acf_delete_cache("format_value/post_id={$post_id}/name={$field['name']}");


		// return
		return $return;

	}

	public function delete_metadata( $name = '', $hidden = false ) {

		// vars
		$return = false;
		$prefix = $hidden ? '_' : '';


		// get post_id info
		$info = $this->get_post_id_info( $this->post_id );


		// bail early if no $this->post_id (acf_form - new_post)
		if ( ! $info['id'] ) {
			return $return;
		}


		// option
		if ( $info['type'] === 'option' ) {

			$name   = $prefix . $this->post_id . '_' . $name;
			$return = delete_option( $name );

			// meta
		} else {

			$name   = $prefix . $name;
			$return = delete_metadata( $info['type'], $info['id'], $name );

		}


		// return
		return $return;

	}

	public function get_post_id_info( $post_id ) {

		// vars
		$info = array(
			'type' => 'post',
			'id'   => 0
		);

		// bail early if no $this->post_id
		if ( ! $this->post_id ) {
			return $info;
		}


		// check cache
		// - this function will most likely be called multiple times (saving loading fields from post)
		//$cache_key = "get_post_id_info/post_id={$this->post_id}";

		//if( acf_isset_cache($cache_key) ) return acf_get_cache($cache_key);


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


			// check if is taxonomy (ACF < 5.5)
			// - avoid scenario where taxonomy exists with name of meta type
			if ( ! in_array( $type, $meta ) && acf_isset_termmeta( $type ) ) {
				$type = 'term';
			}


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


		// update cache
		//acf_set_cache($cache_key, $info);


		// return
		return $info;

	}
}