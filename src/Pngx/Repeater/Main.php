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
	protected $new_meta;
	protected $post_id;
	protected $counter = 0;
	protected $info;
	protected $repeater_fields;

	/**
	 * Pngx__Repeater__Main constructor.
	 */
	public function __construct( $repeater_id, $meta, $post_id, $save = false ) {

		$this->id                = $repeater_id;
		$this->post_id           = $post_id;
		$this->meta[ $this->id ] = is_array( $meta ) ? $meta : array();
		$this->repeater_fields   = apply_filters( 'pngx_meta_repeater_fields', array() );
		$this->init_cycle();

	}

	public function init_cycle() {

		// bail early if no $_POST
		if ( empty( $this->meta ) ) {
			return;
		}

		//echo '<pre>';
		//( $this->meta );
		//echo '</pre>';

		$this->new_meta = $this->cycle_repeaters( $this->meta, null );

		echo '<pre>';
		print_r( $this->meta );
		print_r( $this->new_meta );
		echo '</pre>';
	}

	public function cycle_repeaters( $array, $input ) {

		echo '<br><br>' . key( $array ) . ' ' . ' start-cycle_repeaters<br>';

		$cycle = $array;

		$builder = array();

		$keys = array_keys( $cycle );

		foreach ( $keys as $i ) {

			echo $i . ' array_keys<br>';

			foreach ( $cycle as $k => $value ) {

				echo $k . ' key<br>';
/*
				if ( is_array( $value ) && ( ! is_numeric( $k ) &&  $this->repeater_fields[ $k ]['repeater_type'] && 'field' === $this->repeater_fields[ $k ]['repeater_type'] ) ) {
					log_me( 'here' );
					log_me( $value );

				} else*/

				if ( is_array( $value ) ) {

					echo 'value is array<br>';

					if ( isset( $this->repeater_fields[ $k ] ) ) {
						//$builder[ $k ] = array();
					}

					$builder[ $k ] = $this->cycle_repeaters( $value, "{$input}[{$i}][{$k}]" );

				} else {

					if ( ! is_numeric( $k ) && ! isset( $this->new_meta[ $k ] ) ) {
						$sanitized     = new Pngx__Sanitize( $this->repeater_fields[ $k ]['type'], $value, $this->repeater_fields[ $k ] );
						$builder[ $k ] = $sanitized->result;

					}


					echo $value . ' value<br>';

				}

			}

		}

		echo '<br><br>' . key( $array ) . ' end-cycle_repeaters<br>';

		return $builder;

	}

	public function cycle_repeaters_2( $array, $input ) {

		echo '<br><br>' . key( $array ) . ' ' . ' start-cycle_repeaters<br>';

		$cycle = $array;

		$keys = array_keys( $cycle );

//		echo '<br><br><pre>'; print_r( $keys ); echo  '</pre> array_keys<br>';

		//foreach ( $keys as $i ) {


		foreach ( $cycle as $field_key => $value ) {

			echo $field_key . ' key<br>';

			$k = $field_key;

			if ( is_array( $value ) ) {
				echo 'value is array<br>';

				$this->new_meta[] = $field_key;

				if ( is_numeric( key( $value ) ) && $field_key ) {
					$this->new_meta[ $field_key ] = $field_key;
				}

				$this->cycle_repeaters( $value, "{$input}[{$i}][{$k}]" );

			} else {

				$this->new_meta = "{$input}[{$i}][{$k}]" . $value;

				echo $value . ' value<br>';

			}

			//print_r( $value );
			//echo ' value<br>';
			// bail early if not found
			/*			if ( ! $this->repeater_fields[ $field_key ] ) {
							log_me( 'bail no field' );
							continue;
						}*/

			// get repeaters field
			//$field = $this->repeater_fields[ $field_key ];
			//$input = $field_key;
			//$input = '$this->meta[' . $field_key . ']';

			//echo ' field<br>';
			//print_r( $field );
			//echo ' field<br>';
			//echo $input . ' input<br>';

			//$this->cycle_repeaters( $value, $field, $input );

		}

		//}

//		$this->counter++;
		echo '<br><br>' . key( $array ) . ' end-cycle_repeaters<br>';

	}
}