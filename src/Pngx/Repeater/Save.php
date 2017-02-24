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
class Pngx__Repeater__Save {


	protected $repeater_fields;


	function __construct( $repeater_fields ) {

		$this->repeater_fields = $repeater_fields;

	}

/*
 *
 * http://stackoverflow.com/questions/6785355/convert-multidimensional-array-into-single-array
 *https://www.codecademy.com/en/forum_questions/556d9d0ad3292f03fb000558
 * http://php.net/manual/en/function.array-walk-recursive.php
 */

	public function save( $meta = array(), $new, $i = 0 ) {

		log_me( 'save_' . $i );

		//log_me( $meta );

		if ( ! is_array( $meta ) ) {
			return;
		}

		//store current $i
		$c_i = $i;

		//increase to next level
		$i ++;

		foreach ($meta as $key => $level ) {

			//log_me( 'sanitze field' );
			//log_me($key);
			//log_me(isset( $this->repeater_fields[ $key ] ) );
			//log_me($level);

			//$new[$key];

			/*if ( is_numeric($key) )  {
				log_me('possible multiple');
				log_me($key);
				$new[$key];
			} else {
				$new[$key];
			}*/

			// if repeater go through
			if
			(
				isset( $this->repeater_fields[ $key ]['type'] )
				&& 'repeater' === $this->repeater_fields[ $key ]['type']
				&& is_array( $level )
			) {
				//log_me( 'repeater field' );
				//log_me($key);
				$new[] = $key;
				self::save( $level, $new, $i );
			}

			// if field sanitize

			if
			(
				isset( $this->repeater_fields[ $key ]['type'] )
				&& ! is_array( $level )
			) {
				//log_me( 'sanitze field' );
				//log_me($key);
				//log_me($level);
				$sanitized = new Pngx__Sanitize( $this->repeater_fields[ $key ]['type'], $level, $this->repeater_fields[ $key ] );
				log_me( 'sanitzed field' );
				log_me($new);
				log_me($sanitized->result);

			}

			if
			(
				! isset( $this->repeater_fields[ $key ]['type'] )
				&& is_array( $level )
			) {
				//log_me( 'repeaters' );
				//log_me($key);

				//$new[] = $key;

				self::save( $level, $new, $i );
			}

		}

		return $new;

	}

}