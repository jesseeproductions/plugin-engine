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

		/*		echo '<pre>';
				print_r( $this->meta );
				print_r( $this->new_meta );
				echo '</pre>';*/
	}

	public function cycle_repeaters( $array, $input ) {

		//	echo '<br><br>' . key( $array ) . ' ' . ' start-cycle_repeaters<br>';

		$cycle = $array;

		$builder = array();

		$keys = array_keys( $cycle );

		echo 'div class="' . key( $array ) . ' ' . $this->counter ++ . '" <br>';

		foreach ( $keys as $i ) {

			//	echo $i . ' array_keys<br>';

			foreach ( $cycle as $k => $value ) {

				//echo $k . ' key<br>';

				if ( is_array( $value ) && ( isset( $this->repeater_fields[ $k ]['repeater_type'] ) && 'single-field' === $this->repeater_fields[ $k ]['repeater_type'] ) ) {
					//echo $k . ' key<br>';

					$builder[ $k ] = $this->field_repeater( $value, $k );


				} elseif ( is_array( $value ) ) {

					//echo 'value is array<br>';

					/*					if ( isset( $this->repeater_fields[ $k ] ) ) {
											//$builder[ $k ] = array();
										}*/

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

		//echo '<br><br>' . key( $array ) . ' end-cycle_repeaters<br>';

		echo '/div class="' . key( $array ) . '" <br>';

		return $builder;

	}

	public function field_repeater( $array, $k ) {

		$cycle = $array;

		$builder = array();

		foreach ( $cycle as $value ) {

			$sanitized = new Pngx__Sanitize( $this->repeater_fields[ $k ]['type'], $value, $this->repeater_fields[ $k ] );
			$builder[] = $sanitized->result;

		}

		return $builder;

	}
}