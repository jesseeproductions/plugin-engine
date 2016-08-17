<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Options' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Options
 * Create, Save, Display Options
 */
class Pngx__Admin__Options {

	protected static $instance;
	/*
	* Tab Sections
	*/
	protected $sections;
	/*
	* Checkbox Fields
	*/
	protected $checkboxes;
	/*
	* Option Fields
	*/
	public $fields;

	/*
	* Options Page Slug
	*/
	protected $options_slug = 'plugin-engine-options';

	/*
	* Options ID
	*/
	protected $options_id = 'plugin_engine_options';

	/*
	* Field Prefix
	*/
	protected $field_prefix = 'pngx_';


	/*
	* Construct
	*/
	public function __construct() {

		$this->checkboxes = array();
		$this->fields     = $this->get_option_fields();
		$this->set_sections();

		add_action( 'admin_menu', array( $this, 'options_page' ) );
		add_action( 'admin_init', array( $this, 'register_options' ), 15 );
		add_action( 'init', array( 'Pngx__Admin__Fields', 'flush_permalinks' ) );

		if ( ! get_option( $this->options_id ) ) {
			add_action( 'admin_init', array( $this, 'set_defaults' ), 10 );
		}

		add_action( 'pngx_before_option_form', array( __CLASS__, 'display_options_header' ), 5 );
		add_action( 'pngx_after_option_form', array( __CLASS__, 'display_options_footer' ), 5 );

	}

	/**
	 * Setup Options Page
	 */
	public function options_page() {

		$admin_page = add_submenu_page( 'options-general.php', // parent_slug
			__( 'Plugin Engine Options', 'plugin-engine' ), // page_title
			__( 'Plugin Engine', 'plugin-engine' ), // menu_title
			'manage_options', // capability
			$this->options_slug, // menu_slug
			array( $this, 'display_fields' ) // function
		);

		//add_action( 'admin_print_scripts-' . $admin_page,  array( 'Pngx__Admin__Assets', 'load_assets' ) );

	}

	/*
	* Register Options
	*/
	public function register_options() {

		register_setting( $this->options_id, $this->options_id, array( $this, 'validate_options' ) );

		foreach ( $this->sections as $slug => $title ) {
			//set to this an empty method as this ignores the WordPress Setting Sections
			add_settings_section( $slug, $title, array( $this, 'display_section' ), $this->options_slug );
		}

		foreach ( $this->fields as $id => $option ) {
			$option['id'] = $id;
			$this->create_field( $option );
		}

	}

	/*
	* Display Section
	*/
	public function display_section() {
		//Empty Method that is need to prevent infinite redirects
	}

	/*
	* Get Section Tabs
	*/
	public function set_sections() {
		//Section Tab Headings
		$this->sections['defaults'] = __( 'Defaults', 'plugin-engine' );
	}

	/*
	* Get Section Tabs
	*/
	public function get_sections() {
		return $this->sections;
	}

	/*
	* Individual Fields Framework
	*/
	public function create_field( $args = array() ) {

		$defaults = array(
			'id'        => 'default_id',
			'title'     => __( 'Default' ),
			'desc'      => __( 'This is a default description.' ),
			'section'   => 'general',
			'alert'     => '',
			'condition' => '',
			'std'       => '',
			'type'      => 'text',
			'choices'   => array(),
			'class'     => '',
			'imagemsg'  => '',
			'size'      => 35,
			'toggle'    => array(),
		);

		$field_args = wp_parse_args( $args, $defaults );

		if ( $field_args['type'] == 'checkbox' ) {
			$this->checkboxes[] = $field_args['id'];
		}

		add_settings_field( $field_args['id'], $field_args['title'], array( $this, 'display_field' ), $this->options_slug, $field_args['section'], $field_args );
	}

	/*
	* Option Fields
	*/
	public function get_option_fields() {

		//Expiration
		$fields['header_expiration'] = array(
			'section' => 'defaults',
			'title'   => '',
			'desc'    => __( 'Heading', 'coupon-creator' ),
			'type'    => 'heading'
		);
		$fields['plugin_text_field'] = array(
			'section' => 'defaults',
			'title'   => __( 'Text Field', 'plugin-engine' ),
			'desc'    => __( 'Enter Text', 'plugin-engine' ),
			'std'     => '',
			'type'    => 'text',
			'class'   => ''
		);

		return $fields;

	}

	/*
	* Validate Options
	*/
	public function validate_options( $input ) {
		$options = get_option( $this->options_id );
		//log_me( 'validate' );
		//log_me( $options );
		//log_me( $input );
		//log_me( $_POST );
		$clean = '';

		//if Reset is Checked then delete all options
		if ( ! isset( $input['reset_theme'] ) ) {

			//If No CheckBox Sent, then Unset the Option
			$options = get_option( $this->options_id );

			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[ $id ] ) && ! isset( $input[ $id ] ) ) {
					unset( $options[ $id ] );
				}
			}

			//$id is option name - $option is array of values from $this->fields
			foreach ( $this->fields as $id => $option ) {

				if ( isset( $option['class'] ) ) {
					// Change Permalink Class Options to Lowercase
					if ( $option['class'] == 'permalink' ) {
						$input[ $id ] = str_replace( " ", "-", strtolower( trim( $input[ $id ] ) ) );
						//if option is new then set to flush permalinks
						if ( $options[ $id ] != $input[ $id ] ) {
							$permalink_change = 'pngx_permalink_change';
							update_option( $permalink_change, true );
						}
					}
				}
				//Prevent Placeholder From Saving in Option for Text Areas
				if ( $option['type'] == "textarea" ) {
					if ( $input[ $id ] == $option['std'] ) {
						$input[ $id ] = false;
					}
				}

				// Create Separate License Option and Status
				if ( $option['type'] == 'license' && isset( $input[ $id ] ) ) {

					//Send Input to Sanitize Class, will return sanitized input or no input if no sanitization method
					$sanitize = new Pngx__Sanitize( $option['type'], $input[ $id ], $option );

					$license_info = array();

					//License WP Option Name
					$license = $this->field_prefix . $option['class'];

					//License Key
					$license_info['key'] = $sanitize->result;

					//Get Existing Option
					$existing_license = get_option( $license );

					if ( ! $existing_license['key'] ) {

						update_option( $license, $license_info );

					} elseif ( $existing_license['key'] && $existing_license['key'] != $license_info['key'] ) {

						delete_option( $license );

						update_option( $license, $license_info );

					}

					// Remove to not save with Option Array
					$input[ $id ] = "";
				}

				// Handle License Status
				if ( $option['type'] == 'license_status' ) {
					// Remove to not save with Option Array
					$input[ $id ] = "";
				}

				// Sanitization Filter for each Option Type
				if ( isset( $input[ $id ] ) && $option['type'] != 'license' && $option['type'] != 'license_status' ) {

					//Send Input to Sanitize Class, will return sanitized input or no input if no sanitization method
					$sanitize = new Pngx__Sanitize( $option['type'], $input[ $id ], $option );
					//log_me( 'are we saving?' );
					//log_me( $sanitize );
					//Set Sanitized Input in Array
					$clean[ $id ] = $sanitize->result;

				}

			}
			//log_me( '$clean' );
			//log_me( $clean );
			return $clean;
		}

		//Set Option to Flush Permalinks on Next Load as Reset was checked
		update_option( 'pngx_permalink_change', true );

		return false;

	}

	/*
	* Display Fields
	*/
	public function display_fields() {

		global $wp_version;

		//Create Array of Tabs and Localize to Meta Script
		$tabs_array = array();

		foreach ( $this->sections as $tab_slug => $tab ) {
			$tabs_array[ $tab ] = $tab_slug;
		}

		$tab_data = array(
			'tabs'           => $tabs_array,
			'update_message' => get_settings_errors(),
			'id' => 'pngx-options',
			'wp_version'     => $wp_version,
		);

		echo '<div class="wrap pngx-wrapper">';

		/**
		 * Before Plugin Engine Options Form
		 *
		 * @param string $this ->options_slug options page string
		 */
		do_action( 'pngx_before_option_form', $this->options_slug );

		echo '<form action="options.php" method="post">';

		settings_fields( $this->options_id );

		echo '<div class="pngx-tabs" ' . Pngx__Admin__Fields::toggle( $tab_data, null ) . '>';

		echo '<ul class="pngx-tabs-nav">';

		foreach ( $this->sections as $section_slug => $section ) {
			echo '<li><a href="#' . $section_slug . '">' . $section . '</a></li>';
		}

		echo '</ul>';

		do_settings_sections( $_GET['page'] );

		echo '</div><!-- .pngx-tabs -->';

		echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes' ) . '" /></p>';

		echo '</form>';

		/**
		 * After Plugin Engine Options Form
		 *
		 * @param string $this ->options_slug options page string
		 */
		do_action( 'pngx_after_option_form', $this->options_slug );

		echo '</div><!-- .wrap -->';

	}


	/*
	 * Options Header
	 *
	 */
	public static function display_options_header( $slug ) {

		echo '<h1>Plugin Engine Options</h1>';

	}

	/*
	 * Option Footer Fields
	 *
	 */
	public static function display_options_footer( $slug ) {

		echo '<p style="text-align:right;">&copy; ' . date( "Y" ) . ' Jessee Productions, LLC</p>';

	}

	/*
	* Display Individual Fields
	*/
	public function display_field( $field = array() ) {

		global $wp_version;

		$options = get_option( $this->options_id );
		//log_me( 'display_field' );
		//log_me( $options );
		//log_me( $field );
		if ( ! isset( $options[ $field['id'] ] ) && 'checkbox' != $field['type'] ) {
			$options[ $field['id'] ] = $field['std'];
		} elseif ( ! isset( $options[ $field['id'] ] ) ) {
			$options[ $field['id'] ] = 0;
		}

		Pngx__Admin__Fields::display_field( $field, $options, $this->options_id, false, false, false, $wp_version );

	}

	/*
	 * Set Default Options
	 */
	public function set_defaults() {
		$this->initialize_options();
	}

	/*
	* Initialize Options and Default Values
	*/
	public function initialize_options() {

		$default_options = array();

		//log_me('fields');
		//log_me($this->fields);
		//log_me($this->options_id);

		if ( is_array( $this->fields ) ) {
			foreach ( $this->fields as $id => $option ) {

				if ( $option['type'] != 'heading' && isset( $option['std'] ) ) {

					//Sanitize Default
					$pngx_sanitize = new Pngx__Sanitize( $option['type'], $option['std'], $option );

					//Set Sanitized Input in Array
					$default_options[ $id ] = $pngx_sanitize->result;
				}

			}
			//log_me('saving defaults?');
			update_option( $this->options_id, $default_options );
		}

	}

	/**
	 * Static Singleton Factory Method
	 *
	 * @return Pngx__Admin__Options
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			$className      = __CLASS__;
			self::$instance = new $className;
		}

		return self::$instance;
	}

}