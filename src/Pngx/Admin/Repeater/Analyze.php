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
class Pngx__Admin__Repeater__Analyze {

	/**
	 * Repeating Columns if True
	 *
	 * @var
	 */
	public $repeating_column;


	public function analyze( $meta ) {


	}

	public function array_depth( array $array ) {
		$max_depth = 1;

		foreach ( $array as $value ) {
			if ( is_array( $value ) ) {
				$depth = self::array_depth( $value ) + 1;

				if ( $depth > $max_depth ) {
					$max_depth = $depth;
				}
			}
		}

		return $max_depth;
	}

	public function array_depth_2( array $array ) {
		$max_indentation = 1;

		$array_str = print_r( $array, true );
		$lines     = explode( "\n", $array_str );

		foreach ( $lines as $line ) {
			$indentation = ( strlen( $line ) - strlen( ltrim( $line ) ) ) / 4;

			if ( $indentation > $max_indentation ) {
				$max_indentation = $indentation;
			}
		}

		return ceil( ( $max_indentation - 1 ) / 2 ) + 1;
	}

	//Find the value of a Key
	public function seekKey( $haystack, $needle ) {
		foreach ( $haystack as $key => $value ) {
			if ( $key == $needle ) {
				$output = $value;
			} elseif ( is_array( $value ) ) {
				$output = self::seekKey( $value, $needle );
			}
		}

		return $output;
	}

// Find the Key that matches the Value
	public function seekValue( $haystack, $needle ) {
		foreach ( $haystack as $key => $value ) {
			if ( $key == $needle ) {
				$output = $value;
			} elseif ( is_array( $value ) ) {
				$output = self::seekValue( $value, $needle );
			}
		}

		return $output;
	}

	public function makeNestedList( array $Array ) {
		$Output = '<ul>';
		foreach ( $Array as $Key => $Value ) {
			$Output .= "<li><strong>{$Key}: </strong>";
			if ( is_array( $Value ) ) {
				$Output .= self::makeNestedList( $Value );
			} else {
				$Output .= $Value;
			}
			$Output .= '</li>';
		}
		$Output .= '</ul>';

		return $Output;
	}
}