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

	protected $handler;
	protected $id;
	protected $post_id;
	protected $meta;
	protected $type;
	protected $repeater_fields;

	protected $new_meta;
	protected $counter = 0;
	protected $info;


	/**
	 * Pngx__Repeater__Main constructor.
	 */
	public function __construct( $repeater_id, $meta, $post_id, $type ) {

		$this->id                = $repeater_id;
		$this->post_id           = $post_id;
		$this->meta[ $this->id ] = is_array( $meta ) ? $meta : array();
		$this->repeater_fields   = apply_filters( 'pngx_meta_repeater_fields', array() );
		$this->type              = $type;
		if ( 'admin' === $this->type ) {
			$this->handler = new Pngx__Repeater__Handler__Admin();
		} elseif ( 'save' === $this->type ) {
			$this->handler = new Pngx__Repeater__Handler__Save();
		} elseif ( 'front-end' === $this->type ) {
			$this->handler = new Pngx__Repeater__Handler__Front_End();
		}
		$this->init_cycle();

	}

	//todo
	/*
	 * setup to handle 3 different types and make a class for each, so the object just focuses on one
	 * admin - loading of fields to edit - repeaters
	 * save - save and sanitize the values
	 * frontend - display the values - need to be able to use templates
	 *
	 *
	 * pro admin - drag and reorder all items
	 * pro frontend - more templates
	 */

	public function init_cycle() {

		// bail early if no $_POST
		if ( empty( $this->meta ) ) {
			return;
		}

//		echo '<pre>';
//		print_r( $this->meta );
//		echo '</pre>';


		$this->new_meta = $this->cycle_repeaters( $this->meta, null );

//		echo '<pre>';
//		print_r( $this->new_meta );
//		echo '</pre>';
	}

	public function cycle_repeaters( $array, $input, $name = null ) {

		$cycle = $array;

		$builder = array();

		$keys = array_keys( $cycle );

		//$is_numeric = $this->is_key_numeric( $keys );

		//$key_count = 0;

		//log_me( 'starts' );
		//log_me( $keys );//[0] => wpe_menu_section
		//name loop
		foreach ( $keys as $i ) {

			//log_me( $i );//wpe_menu_section
			//log_me( $cycle[ $i ] ); //array

			if ( is_array( $cycle[ $i ] ) && ( isset( $this->repeater_fields[ $i ]['repeater_type'] ) && 'single-field' === $this->repeater_fields[ $i ]['repeater_type'] ) ) {

				$builder[ $i ] = $this->field_repeater( $cycle[ $i ], $i, "{$input}[{$i}]" );

			} elseif ( is_array( $cycle[ $i ] ) ) {

				$subkeys = array_keys( $cycle[ $i ] );

				//log_me( $subkeys );// [0] => 0

//https://www.google.com/search?q=php+multidimensial+array+runs+through+it+twice&ie=utf-8&oe=utf-8#q=php+multidimensional+array+runs+through+it+twice&*
				echo $this->handler->display_repeater_open( $i, $this->repeater_fields[ $i ]['repeater_type'] );

				//number loop
				foreach ( $subkeys as $subkey ) {

					//log_me( $subkey ); //0
					//log_me( $cycle[ $i ][ $subkey ] ); //0

					$send_input = "{$input}[{$i}][{$subkey}]";
					if ( ! $input ) {
						$send_input = "{$i}[{$subkey}]";
					}

					echo $this->handler->display_repeater_item_open( $i, $this->repeater_fields[ $i ]['repeater_type'] );

					$builder[ $i ][ $subkey ] = $this->cycle_repeaters( $cycle[ $i ][ $subkey ], $send_input );

					echo $this->handler->display_repeater_item_close( $i, $this->repeater_fields[ $i ]['repeater_type'] );

				}

				echo $this->handler->display_repeater_close( $i );


			} else {

				if ( ! is_numeric( $i ) && ! isset( $this->new_meta[ $i ] ) ) {

					$sanitized     = new Pngx__Sanitize( $this->repeater_fields[ $i ]['type'], $cycle[ $i ], $this->repeater_fields[ $i ] );
					$builder[ $i ] = $sanitized->result;

					echo $this->handler->display_field( $this->repeater_fields[ $i ], $cycle[ $i ] );


				}

			}

		}


		return $builder;

	}

	public function field_repeater( $array, $k, $input ) {

		$cycle = $array;

		$builder = array();

		echo '<br>opendiv class="' . $k . '" <br>';
		//todo add method to handle opening ( admin, saving, and front end )
		foreach ( $cycle as $value ) {

			$sanitized = new Pngx__Sanitize( $this->repeater_fields[ $k ]['type'], $value, $this->repeater_fields[ $k ] );

			//echo 'name "' . $input . '[' . $k . '][]" <br>';
			echo $value . ' value<br>';
			$builder[] = $sanitized->result;

		}

		echo '/div class="' . $k . '" <br>';

		//todo add method to handle closing ( admin, saving, and front end )

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