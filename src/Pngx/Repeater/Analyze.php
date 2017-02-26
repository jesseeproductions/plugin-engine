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

	todo build what the name string should be for each level

	level0                  level1              level2          level3              level4
	wpe_menu_section[0][wpe_menu_column][0][wpe_menu_items][0][wpe_menu_r_cost][0][wpe_menu_price][]

	todo when field is saving, admin display, or front end I should be able to detect what level it is on


	*/
	public function build_array( $i = 0 ) {

		$level_fields = $this->repeater_fields;

		//if we passed a field id only then set to array
		if ( ! is_array( $level_fields ) && isset( $this->repeater_fields[ $fields ] ) ) {
			$level_fields = array( $this->repeater_fields[ $fields ] );
		} elseif ( ! is_array( $level_fields ) && ! isset( $this->repeater_fields[ $fields ] ) ) {
			return;
		}

		// setup initial counts
		if ( ! isset( $this->{'level_' . $i} ) ) {
			$this->{'level_' . $i}['counts'] = $this->set_level_counts();
		}

		// analyze level
		foreach ( $level_fields as $field ) {

			if ( ! isset( $field['id'] ) || ! isset( $this->repeater_fields[ $field['id'] ] ) ) {
				continue;
			}

			$this->{'level_' . $i}[] = array(
				'id'              => $field['id'],
				'type'            => $this->repeater_fields[ $field['id'] ]['type'],
				'repeater_type'   => isset( $this->repeater_fields[ $field['id'] ]['repeater_type'] ) ? $this->repeater_fields[ $field['id'] ]['repeater_type'] : '',
				'repeater_fields' => isset( $this->repeater_fields[ $field['id'] ]['repeater_fields'] ) ? $this->repeater_fields[ $field['id'] ]['repeater_fields'] : '',
			);

			if ( isset( $this->repeater_fields[ $field['id'] ]['repeater_type'] ) ) {
				'section' === $field['repeater_type'] ? $this->{'level_' . $i}['counts']['sections'] ++ : false;
				'column' === $field['repeater_type'] ? $this->{'level_' . $i}['counts']['columns'] ++ : false;
				'field' === $field['repeater_type'] ? $this->{'level_' . $i}['counts']['fields'] ++ : false;
			}

			$this->{'level_' . $i}['counts']['all'] ++;

		}

		// iterate to next level if repeater fields
		// @formatter:off
		if (
		0 < $this->{'level_' . $i}['counts']['sections']
		|| 0 < $this->{'level_' . $i}['counts']['columns']
		|| 0 < $this->{'level_' . $i}['counts']['fields']
		) {
		// @formatter:on

			//store current $i
			$c_i = $i;

			//increase to next level
			$i ++;

			foreach ( $this->{'level_' . $c_i} as $field ) {

				if ( isset( $field['repeater_fields'] ) && is_array( $field['repeater_fields'] ) ) {

					self::analyze( $field['repeater_fields'], $i );

				}

			}
		}

		return;
	}


	/*  level0                  level1              level2          level3              level4
	 * wpe_menu_section[0][wpe_menu_column][0][wpe_menu_items][0][wpe_menu_r_cost][0][wpe_menu_price][]
	 *  level0                  level1              level2          level3              level4
	 * wpe_menu_section[0][wpe_menu_column][0][wpe_menu_items][0][wpe_menu_items][0][wpe_menu_name]
what if there are two repeaters in repeater_fields

	send in array of fields or a single field and convert to array
	store fields and then if deeper go deeper, but when it comes back check if at this level to reset the counter

	todo build what the name string should be for each level

	level0                  level1              level2          level3              level4
	wpe_menu_section[0][wpe_menu_column][0][wpe_menu_items][0][wpe_menu_r_cost][0][wpe_menu_price][]

	todo when field is saving, admin display, or front end I should be able to detect what level it is on


	*/
	public function analyze( $fields, $i = 0 ) {

		$level_fields = $fields;

		//if we passed a field id only then set to array
		if ( ! is_array( $level_fields ) && isset( $this->repeater_fields[ $fields ] ) ) {
			$level_fields = array( $this->repeater_fields[ $fields ] );
		} elseif ( ! is_array( $level_fields ) && ! isset( $this->repeater_fields[ $fields ] ) ) {
			return;
		}

		// setup initial counts
		if ( ! isset( $this->{'level_' . $i} ) ) {
			$this->{'level_' . $i}['counts'] = $this->set_level_counts();
		}

		// analyze level
		foreach ( $level_fields as $field ) {

			if ( ! isset( $field['id'] ) || ! isset( $this->repeater_fields[ $field['id'] ] ) ) {
				continue;
			}

			$this->{'level_' . $i}[] = array(
				'id'              => $field['id'],
				'type'            => $this->repeater_fields[ $field['id'] ]['type'],
				'repeater_type'   => isset( $this->repeater_fields[ $field['id'] ]['repeater_type'] ) ? $this->repeater_fields[ $field['id'] ]['repeater_type'] : '',
				'repeater_fields' => isset( $this->repeater_fields[ $field['id'] ]['repeater_fields'] ) ? $this->repeater_fields[ $field['id'] ]['repeater_fields'] : '',
			);

			if ( isset( $this->repeater_fields[ $field['id'] ]['repeater_type'] ) ) {
				'section' === $field['repeater_type'] ? $this->{'level_' . $i}['counts']['sections'] ++ : false;
				'column' === $field['repeater_type'] ? $this->{'level_' . $i}['counts']['columns'] ++ : false;
				'field' === $field['repeater_type'] ? $this->{'level_' . $i}['counts']['fields'] ++ : false;
			}

			$this->{'level_' . $i}['counts']['all'] ++;

		}

		// iterate to next level if repeater fields
		// @formatter:off
		if (
		0 < $this->{'level_' . $i}['counts']['sections']
		|| 0 < $this->{'level_' . $i}['counts']['columns']
		|| 0 < $this->{'level_' . $i}['counts']['fields']
		) {
		// @formatter:on

			//store current $i
			$c_i = $i;

			//increase to next level
			$i ++;

			foreach ( $this->{'level_' . $c_i} as $field ) {

				if ( isset( $field['repeater_fields'] ) && is_array( $field['repeater_fields'] ) ) {

					self::analyze( $field['repeater_fields'], $i );

				}

			}
		}

		return;
	}

	protected function set_level_counts() {
		return array(
			'sections' => 0,
			'columns'  => 0,
			'fields'   => 0,
			'all'      => 0,
		);
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


	//Find the value of a Key
	public function seek_key( $haystack, $needle ) {
		foreach ( $haystack as $key => $value ) {
			if ( $key == $needle ) {
				$output = $value;
			} elseif ( is_array( $value ) ) {
				$output = self::seek_key( $value, $needle );
			}
		}

		return $output;
	}

	// Find the Key that matches the Value
	public function seek_value( $haystack, $needle ) {
		foreach ( $haystack as $key => $value ) {
			if ( $key == $needle ) {
				$output = $value;
			} elseif ( is_array( $value ) ) {
				$output = self::seek_value( $value, $needle );
			}
		}

		return $output;
	}

	public function make_nested_list( array $Array ) {
		$Output = '<ul>';
		foreach ( $Array as $Key => $Value ) {
			$Output .= "<li><strong>{$Key}: </strong>";
			if ( is_array( $Value ) ) {
				$Output .= self::make_nested_list( $Value );
			} else {
				$Output .= $Value;
			}
			$Output .= '</li>';
		}
		$Output .= '</ul>';

		return $Output;
	}
}