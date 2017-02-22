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
class Pngx__Repeater__Analyze {


	protected $repeater_fields;


	function __construct( $repeater_fields ) {

		$this->repeater_fields = $repeater_fields;

	}

	/*  level0                  level1              level2          level3              level4
	 * wpe_menu_section[0][wpe_menu_column][0][wpe_menu_items][0][wpe_menu_r_cost][0][wpe_menu_price][]
	 *  level0                  level1              level2          level3              level4
	 * wpe_menu_section[0][wpe_menu_column][0][wpe_menu_items][0][wpe_menu_items][0][wpe_menu_name]
what if there are two repeaters in repeater_fields

	send in array of fields or a single field and convert to array
	store fields and then if deeper go deeper, but when it comes back check if at this level to reset the counter



	*/
	public function analyze( $fields, $i = 0 ) {

		$level_fields = $fields;

		//if we passed a field id only then set to array
		if ( ! is_array( $level_fields ) && $this->repeater_fields[ $fields ] ) {
			$level_fields = array( $this->repeater_fields[ $fields ] );
		} elseif ( ! isset( $this->repeater_fields[ $fields ] ) ) {
			return;
		}

		if ( ! isset( $this->{'level_' . $i } ) ) {
			$this->{'level_' . $i}['counts'] = array(
				'sections' => 0,
				'columns'   => 0,
				'fields'    => 0,
				'all'      => 0,
			);
		}
		foreach ( $level_fields as $field ) {

				$this->{'level_' . $i}[] = array(
					'id'            => $field['id'],
					'type'          => $field['type'],
					'repeater_type' => isset( $field['repeater_type'] ) ? $field['repeater_type'] : '',
				);

			$this->{'level_' . $i}['counts']['sections'] = 'sections' == $field['repeater_type'] ? $this->{'level_' . $i}['counts']['sections']++ : $this->{'level_' . $i}['counts']['sections'];
			$this->{'level_' . $i}['counts']['columns'] = 'column' == $field['repeater_type'] ? $this->{'level_' . $i}['counts']['columns']++ : $this->{'level_' . $i}['counts']['columns'];
			$this->{'level_' . $i}['counts']['fields'] = 'field' == $field['repeater_type'] ? $this->{'level_' . $i}['counts']['fields']++ : $this->{'level_' . $i}['counts']['fields'];
			$this->{'level_' . $i}['counts']['all'] = 'field' == $field['repeater_type'] ? $this->{'level_' . $i}['counts']['all']++ : $this->{'level_' . $i}['counts']['all'];


		}

				//self::analyze( $repeater_id, $i );
		return;
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