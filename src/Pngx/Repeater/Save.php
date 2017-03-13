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
 *
 *
 * http://stackoverflow.com/questions/30455995/sanitize-desanitize-multidimensional-array
 * http://brandonwamboldt.github.io/utilphp/
 * https://github.com/search?l=PHP&q=Multidimensional+Array%E2%80%8E&type=Repositories&utf8=%E2%9C%93
 * http://stackoverflow.com/questions/31394497/iterate-multidimensional-array-recursively-and-return-same-array-structure-and-i
 * http://stackoverflow.com/questions/8587341/recursive-function-to-generate-multidimensional-array-from-database-result
 * https://section214.com/2015/05/repeatable-fields-in-meta-boxes/
 */
	public function logme($item, $key) {

		log_me( "$key holds $item" );
	}

	public function walk( $meta = array() ) {
		log_me( "walk" );
		//array_walk_recursive($meta, array( $this, 'logme') );

		log_me( count( $meta ) );
		log_me( count( $meta[0] ) );
		log_me( count( $meta[0]['wpe_menu_column'] ) );
		log_me( count( $meta[0]['wpe_menu_column'][0] ) );
		log_me( count( $meta[0]['wpe_menu_column'][0]['wpe_menu_items'] ) );
		log_me( count( $meta[0]['wpe_menu_column'][0]['wpe_menu_items'][0] ) );
		log_me( $meta );

	}


	public function save( $meta = array(), $new, $i = 0 ) {


		if ( ! is_array( $meta ) ) {
			return;
		}

		//store current $i
		//$c_i = $i;

		//increase to next level
		//$i ++;

		foreach ($meta as $key => $level ) {

			// if repeater go through
			if
			(
				isset( $this->repeater_fields[ $key ]['type'] )
				&& 'repeater' === $this->repeater_fields[ $key ]['type']
				&& is_array( $level )
			) {
				log_me( 'repeater field' );
				log_me( $key );
				//log_me( $new );
				//log_me( count( $level ) );
				//$new[$key] = '';
				//self::save( $level, $new, $i );

				for ( $l = 0; $l < count( $level ); $l ++ ) {

					//self::iterate( $level[ $l ] );
					//log_me( 'repeater field for loop' );
					//log_me( $level[ $l ] );
					//log_me( $new[$key][$l] );
					self::save( $level[ $l ], $new );

				}

			}

			// if field sanitize

			if
			(
				isset( $this->repeater_fields[ $key ]['type'] )
				&& ! is_array( $level )
			) {

				//log_me( 'sanitze field' );
				//log_me($key);
				//log_me($new);
				//log_me($level);
				$sanitized = new Pngx__Sanitize( $this->repeater_fields[ $key ]['type'], $level, $this->repeater_fields[ $key ] );
				log_me('new field');
				log_me($new);
				log_me($key);
				$new[$key] = $sanitized->result;
				//log_me($sanitized->result);

			}


			//repeating value fields
			if
			(
				! isset( $this->repeater_fields[ $key ]['type'] )
				&& is_array( $level )
			) {
				//log_me( 'repeaters' );
				//log_me($key);
				//log_me(count($level));
				//$new[] = $key;

				self::save( $level, $new, $i );
			}

		}

		return $new;

	}


	public function iterate( $meta = array() ) {

			log_me($meta);


	}

}