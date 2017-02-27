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
	public function __construct( $repeater_id, $meta, $post_id, $save = false ) {

		$this->id              = $repeater_id;
		$this->post_id         = $post_id;
		$this->meta            = is_array( $meta ) ? $meta : array();
		$this->repeater_fields = apply_filters( 'pngx_meta_repeater_fields', array() );

		if ( $save ) {

			$this->update_value( $this->meta , $this->repeater_fields[$this->id] );
			$this->cycle_repeaters( $this->repeater_fields[$this->id] );

		}

	}
	public function cycle_repeaters( $field ) {

		if ( isset( $field[ 'repeater_fields'] ) ) {

			foreach ( $field[ 'repeater_fields'] as $repeater_field ) {

				//todo see if this function in acf works to get the levels value and if so connect here
				//$meta = $this->update_sub_field();

				$this->update_value( $this->meta, $repeater_field );

				if ( 'repeater' === $repeater_field['type'] ) {
				    $this->cycle_repeaters( $repeater_field );
				}

			}
		}

	}

	public function update_sub_field( $selector, $value, $post_id = false ) {

		// vars
		$sub_field = false;


		// filter post_id
		$post_id = acf_get_valid_post_id( $post_id );


		// get sub field
		if( is_array($selector) ) {

			$sub_field = acf_maybe_get_sub_field( $selector, $post_id, false );

		} else {

			$sub_field = get_row_sub_field( $selector );

		}


		// bail early if no sub field
		if( !$sub_field ) return false;


		// update
		return acf_update_value( $value, $post_id, $sub_field );

	}

	//public function update_value( $value, $this->post_id, $field ) {
	public function update_value( $value, $field ) {
		global $countermenu;
		log_me('valuemenu');
		log_me($countermenu++);
		//log_me($value);
		log_me($field['id']);
		//log_me($field);
		// bail early if no sub fields
		if ( empty( $field['repeater_fields'] ) ) {
			return $value;
		}
		//log_me( 'update value obj1' );

		// vars
		$new_value = 0;
		$old_value = (int) $this->get_metadata( $field['id'] );


		// update sub fields
		if ( ! empty( $value ) ) {
			$i = - 1;
		//	log_me( 'update value obj2' );
			// remove acfcloneindex
			//if ( isset( $value['acfcloneindex'] ) ) {

			//	unset( $value['acfcloneindex'] );

			//}

			// loop through rows
			foreach ( $value as $row ) {
				$i ++;
//log_me('row');
//log_me($row);
				// bail early if no row
				if ( ! is_array( $row ) ) {
				log_me('bail');
					continue;
				}


				// update row
				$this->update_row( $row, $i, $field );


				// append
				$new_value ++;

			}

		}
		//log_me( 'update value obj3' );

		// remove old rows
		if ( $old_value > $new_value ) {

			// loop
			for ( $i = $new_value; $i < $old_value; $i ++ ) {

				$this->delete_row( $i, $field );

			}

		}

		//log_me( 'update value obj4' );
		// save false for empty value
		if ( empty( $new_value ) ) {
			$new_value = '';
		}

		//log_me( 'update value obj5' );
		//log_me( $new_value );
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
		//log_me('menusub');
		//log_me($field['repeater_fields']);

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
			$this->update_values( $value, $repeater_fields );

		}


		// return
		return true;

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

	public function update_values( $value = null, $field ) {

		// strip slashes
		//if( acf_get_setting('stripslashes') ) {

		//	$value = stripslashes_deep($value);

		//}


		// filter for 3rd party customization
		//$value = apply_filters( "acf/update_value", $value, $post_id, $field );
		//$value = apply_filters( "acf/update_value/type={$field['type']}", $value, $post_id, $field );
		//$value = apply_filters( "acf/update_value/name={$field['id']}", $value, $post_id, $field );
		//$value = apply_filters( "acf/update_value/key={$field['key']}", $value, $post_id, $field );


		// allow null to delete
		if ( $value === null ) {

			return $this->delete_value( $field );

		}


		// update value
		$return = $this->update_metadata( $field['id'], $value );


		// update reference
		$this->update_metadata( $field['id'], $field['id'], true );


		// clear cache
		//acf_delete_cache("get_value/post_id={$post_id}/name={$field['id']}");
		//acf_delete_cache("format_value/post_id={$post_id}/name={$field['id']}");


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
		$return = $this->delete_metadata( $field['id'] );


		// delete reference
		$this->delete_metadata( $field['id'], true );


		// clear cache
		//acf_delete_cache("get_value/post_id={$post_id}/name={$field['id']}");
		//acf_delete_cache("format_value/post_id={$post_id}/name={$field['id']}");


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
			//if ( ! in_array( $type, $meta ) && acf_isset_termmeta( $type ) ) {
			//	$type = 'term';
			//}


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

	public function get_metadata( $name = '', $hidden = false ) {

		// vars
		$value  = null;
		$prefix = $hidden ? '_' : '';


		// get post_id info
		$info = $this->get_post_id_info( $this->post_id );


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
}