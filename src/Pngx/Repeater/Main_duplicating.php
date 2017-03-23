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

		echo '<pre>';
		print_r( $this->meta );
		echo '</pre>';


		$this->new_meta = $this->cycle_repeaters( $this->meta, null );

//		echo '<pre>';
//		print_r( $this->meta );
//		print_r( $this->new_meta );
//		echo '</pre>';
	}

	public function cycle_repeaters( $array, $input, $name = null ) {

		//	echo '<br><br>' . key( $array ) . ' ' . ' start-cycle_repeaters<br>';

		$cycle = $array;

		$builder = array();

		$keys = array_keys( $cycle );

		$total_count = count( $keys );

		$is_numeric = $this->is_key_numeric( $keys );

		$key_count = 0;

		foreach ( $keys as $i ) {

			if ( $this->is_repeater( $name ) ) {
				//echo '<br>opendiv class="' . $name . ' ' . $this->counter ++ . '" <br>';
			}

			if ( ! $is_numeric && 0 < $key_count ) {
				continue;
			}

			foreach ( $cycle as $k => $value ) {

				if ( is_array( $value ) && ( isset( $this->repeater_fields[ $k ]['repeater_type'] ) && 'single-field' === $this->repeater_fields[ $k ]['repeater_type'] ) ) {

					$builder[ $k ] = $this->field_repeater( $value, $k, "{$input}[{$k}][]" );

				} elseif ( is_array( $value ) ) {

					$name = '';
					if ( ! is_numeric( $k ) ) {
						$name = $k;
					}

					$builder[ $k ] = $this->cycle_repeaters( $value, "{$input}[{$i}]", $name );

				} else {

					if ( ! is_numeric( $k ) && ! isset( $this->new_meta[ $k ] ) ) {
						$sanitized     = new Pngx__Sanitize( $this->repeater_fields[ $k ]['type'], $value, $this->repeater_fields[ $k ] );
						$builder[ $k ] = $sanitized->result;

					}

					//echo '<br>opendiv class="' . $k . ' ' . $this->counter ++ . '" <br>';
					echo $value . ' value<br>';
					//echo '/div class="' . $k . '" <br>';

				}

			}

			if ( $this->is_repeater( key( $array ) ) ) {
				//echo '/div class="' . key( $array ) . '" <br>';
			}

			$key_count ++;

		}


		return $builder;

	}

	public function field_repeater( $array, $k, $input ) {

		$cycle = $array;

		$builder = array();

		//echo '<br>opendiv class="' . $k . ' ' . $this->counter ++ . '" <br>';

		foreach ( $cycle as $value ) {

			$sanitized = new Pngx__Sanitize( $this->repeater_fields[ $k ]['type'], $value, $this->repeater_fields[ $k ] );

			echo $value . ' value<br>';
			$builder[] = $sanitized->result;

		}

		//echo '/div class="' . $k . '" <br>';

		return $builder;

	}

	public function is_key_numeric( $array ) {

		$numeric = 0;

		foreach ( $array as $value ) {

			if ( is_numeric( $value ) ) {

				$numeric ++;

			}

		}

		return $numeric;
	}

	public function is_repeater( $k ) {

		if ( isset( $this->repeater_fields[ $k ]['repeater_type'] ) && in_array( $this->repeater_fields[ $k ]['repeater_type'], array(
				'section',
				'column'
			) )
		) {

			return true;
		}

		return false;
	}
}