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
	protected $clone;
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

	/**
	 * Start the Cycle and Parse through the fields for admin, saving, or front end
	 */
	public function init_cycle() {

		// bail early if no $_POST
		if ( empty( $this->meta ) ) {
			return;
		}

//		echo '<pre>';
//		print_r( $this->meta );
//		echo '</pre>';

		$this->cycle_templates( Pngx__Admin__Field__Repeater::get_empty_fields(),null );

		log_me('clone');
		log_me($this->clone);

		$this->new_meta = $this->cycle_repeaters( $this->meta, null );

		$this->handler->post_cycle( $this->post_id, $this->id, $this->new_meta );

//		echo '<pre>';
//		print_r( $this->new_meta );
//		echo '</pre>';
	}

	/**
	 * Cycle through the multidimensional array of fields
	 *
	 * @param      $array
	 * @param      $input
	 * @param null $name
	 *
	 * @return array
	 */
	public function cycle_repeaters( $array, $input, $name = null ) {

		$cycle = $array;

		$builder = array();

		$keys = array_keys( $cycle );

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

				//log_me( $cycle[ $i ] );// [0] => 0
				//log_me( $subkeys );// [0] => 0

//https://www.google.com/search?q=php+multidimensial+array+runs+through+it+twice&ie=utf-8&oe=utf-8#q=php+multidimensional+array+runs+through+it+twice&*
				$this->handler->display_repeater_open( $i, $this->repeater_fields[ $i ]['repeater_type'] );

				//number loop
				foreach ( $subkeys as $subkey ) {

					// todo need to create a new cycle that build the empty templates at each level and saves to an array
					// todo the $i is the key $this->clone[$i]
					if ( $subkey === 0 ) {
						//log_me( 'here1' );
						//log_me( $subkey );
						//log_me( $i );
						//log_me( $cycle[ $i ] );
					}

					//log_me( $subkey ); //0
					//log_me( $cycle[ $i ][ $subkey ] ); //0

					$send_input = "{$input}[{$i}][{$subkey}]";
					if ( ! $input ) {
						$send_input = "{$i}[{$subkey}]";
					}

					$this->handler->display_repeater_item_open( $i, $this->repeater_fields[ $i ]['repeater_type'] );

					$builder[ $i ][ $subkey ] = $this->cycle_repeaters( $cycle[ $i ][ $subkey ], $send_input );

					$this->handler->display_repeater_item_close( $i, $this->repeater_fields[ $i ]['repeater_type'] );

				}

				$this->handler->display_repeater_close( $i );


			} else {

				if ( ! is_numeric( $i ) && ! isset( $this->new_meta[ $i ] ) ) {

					$sanitized     = new Pngx__Sanitize( $this->repeater_fields[ $i ]['type'], $cycle[ $i ], $this->repeater_fields[ $i ] );
					$builder[ $i ] = $sanitized->result;

					$this->handler->display_field( $this->repeater_fields[ $i ], $cycle[ $i ], "{$input}[{$i}]", $this->post_id  );


				}

			}

		}


		return $builder;

	}

	/**
	 * Handle Repeating Value Fields
	 *
	 * @param $array
	 * @param $k
	 * @param $input
	 *
	 * @return array
	 */
	public function field_repeater( $array, $k, $input ) {

		$cycle = $array;

		$builder = array();

		foreach ( $cycle as $value ) {

			$sanitized = new Pngx__Sanitize( $this->repeater_fields[ $k ]['type'], $value, $this->repeater_fields[ $k ] );

			$builder[] = $sanitized->result;

			$this->handler->display_repeater_field( $this->repeater_fields[ $k ], $sanitized->result, "{$input}[]", $this->post_id );

			//echo 'name "' . $input . '[' . $k . '][]" <br>';
			//echo $value . ' value<br>';

		}

		return $builder;

	}

	/**
	 * Cycle through the multidimensional array of fields
	 *
	 * @param      $array
	 * @param      $input
	 * @param null $name
	 *
	 * @return array
	 */
	public function cycle_templates( $array, $input, $name = null ) {

		$cycle = $array;

		$builder = array();

		$keys = array_keys( $cycle );

		//log_me( 'starts' );
		//log_me( $keys );//[0] => wpe_menu_section
		//name loop
		foreach ( $keys as $i ) {

			$this->clone[$i] = '';
			//log_me( $i );
			//log_me( $i );//wpe_menu_section
			//log_me( $cycle[ $i ] ); //array

			if ( is_array( $cycle[ $i ] ) && ( isset( $this->repeater_fields[ $i ]['repeater_type'] ) && 'single-field' === $this->repeater_fields[ $i ]['repeater_type'] ) ) {

				//$builder[ $i ] = $this->field_repeater( $cycle[ $i ], $i, "{$input}[{$i}]" );

			} elseif ( is_array( $cycle[ $i ] ) ) {

				$subkeys = array_keys( $cycle[ $i ] );

				//log_me( $cycle[ $i ] );// [0] => 0
				//log_me( $subkeys );// [0] => 0

//https://www.google.com/search?q=php+multidimensial+array+runs+through+it+twice&ie=utf-8&oe=utf-8#q=php+multidimensional+array+runs+through+it+twice&*
				//$this->handler->display_repeater_open( $i, $this->repeater_fields[ $i ]['repeater_type'] );

				//number loop
				foreach ( $subkeys as $subkey ) {

					// todo need to create a new cycle that build the empty templates at each level and saves to an array
					// todo the $i is the key $this->clone[$i]
					//log_me( 'subkey' );
					//log_me( $subkey );
					if ( 0 === $subkey ) {
						//log_me( 'here1' );
						//log_me( $subkey );
						//log_me( $i );
						//log_me( $cycle[ $i ] );

						$send_input = "{$input}[{$i}][{$subkey}]";
						if ( ! $input ) {
							$send_input = "{$i}[{$subkey}]";
						}

						$this->clone[$i] .= $this->handler->display_repeater_item_clone_open( $i, $this->repeater_fields[ $i ]['repeater_type'] );

						//log_me( $this->cycle_templates( $cycle[ $i ][ $subkey ], $send_input ) );
						$this->clone[$i] .= $this->cycle_templates( $cycle[ $i ][ $subkey ], $send_input );

						$this->clone[$i] .= $this->handler->display_repeater_item_clone_close( $i, $this->repeater_fields[ $i ]['repeater_type'] );

					}

					//log_me( $subkey ); //0
					//log_me( $cycle[ $i ][ $subkey ] ); //0

				}

				//$this->handler->display_repeater_close( $i );


			} else {

				if ( ! is_numeric( $i ) && ! isset( $this->new_meta[ $i ] ) ) {

					//$sanitized     = new Pngx__Sanitize( $this->repeater_fields[ $i ]['type'], $cycle[ $i ], $this->repeater_fields[ $i ] );
					//$builder[ $i ] = $sanitized->result;

					//$this->handler->display_field( $this->repeater_fields[ $i ], $cycle[ $i ], "{$input}[{$i}]", $this->post_id  );


				}

			}

		}


		return $builder;

	}
}