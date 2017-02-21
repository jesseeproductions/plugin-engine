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

	/**
	 * Total Sections
	 *
	 * @var
	 */
	protected $analyzer;

	/**
	 * Total Sections
	 *
	 * @var
	 */
	protected $meta;

	/**
	 * Total Sections
	 *
	 * @var
	 */
	protected $repeater_fields;


	/**
	 * Total Sections
	 *
	 * @var
	 */
	protected $sections;

	/**
	 * Current Section
	 *
	 * @var
	 */
	protected $current_section;

	/**
	 * Repeating Section if True
	 *
	 * @var
	 */
	protected $repeating_section;

	/**
	 * Column Per Section
	 *
	 * @var
	 */
	protected $columns;

	/**
	 * Current Column
	 *
	 * @var
	 */
	protected $current_column;

	/**
	 * Repeating Columns if True
	 *
	 * @var
	 */
	protected $repeating_column;


	/**
	 * Pngx__Repeater__Main constructor.
	 */
	public function __construct( $repeater_id, $meta, $current_section = 0, $current_column = 0 ) {

		$this->id              = $repeater_id;
		$this->meta            = is_array( $meta ) ? $meta : array();
		$this->repeater_fields = apply_filters( 'pngx_meta_repeater_fields', array() );
		$this->analyzer        = new Pngx__Repeater__Analyze( $this->repeater_fields );
		$this->levels = $this->analyzer->analyze( $this->id, $this->repeater_fields );


		$this->count  = count( $this->meta );
		$this->depth1 = $this->analyzer->array_depth( $this->meta );
		$this->depth2 = $this->analyzer->array_depth_2( $this->meta );
		//$this->makeNestedList  = $this->analyze->makeNestedList( $this->meta );


		$this->sections          = ! empty( $saved_sections ) ? absint( $saved_sections ) : 1;
		$this->current_section   = $current_section;
		$this->columns           = 1;
		$this->current_column    = $current_column;
		$this->repeating_section = true;
		$this->repeating_column  = false;


	}

	public function get_total_sections() {
		return $this->sections;
	}


	public function get_current_section() {
		return $this->current_section;
	}


	public function get_total_columns() {
		return $this->columns;
	}

	public function get_current_column() {
		return $this->current_column;
	}

	public function get_current_sec_col() {
		return '-' . $this->current_section . '-' . $this->current_column;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_meta_id() {
		return $this->id . $this->get_current_sec_col();
	}

	public function get_field_name( $name ) {
		return $this->id . '[' . $this->current_section . '][' . $name . $this->get_current_sec_col() . '][]';
	}

	public function update_section_count() {
		$this->current_section ++;
	}

	public function set_columns( $count = 0 ) {
		$this->columns = absint( $count );
	}

	public function update_column_count() {
		$this->current_column ++;
	}

	public function reset_column_count() {
		$this->current_column = 0;
	}

	public function get_repeating_sections_status() {
		return $this->repeating_section;
	}

	public function get_repeating_columns_status() {
		return $this->repeating_column;
	}

	public function repeating_sections() {
		$this->repeating_section = true;
		$this->repeating_column  = false;
	}

	public function repeating_columns() {
		$this->repeating_section = false;
		$this->repeating_column  = true;
	}
}