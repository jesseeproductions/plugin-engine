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
 * Create, Save, Display Options for a Plugin
 */
class Pngx__Admin__Options {

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
	* Construct
	*/
	public function __construct() {

		$this->checkboxes = array();
		$this->fields = Pngx__Admin__Fields::get_option_fields();
		$this->set_sections();

		add_action( 'admin_menu', array( $this, 'options_page' ) );
		add_action( 'admin_init', array( $this, 'register_options' ), 15 );
		//add_action( 'admin_init', array( __CLASS__, 'flush_permalinks' ) );

		if ( ! get_option( 'pngx_options' ) ) {
			add_action( 'admin_init', array( $this, 'set_defaults' ), 10 );
		}

	}

	/*
	* Admin Options Page
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

		register_setting( 'plugin_engine_options', 'plugin_engine_options', array( $this, 'validate_options' ) );

		foreach ( $this->sections as $slug => $title ) {
			//set to this an empty method as this ignores the WordPress Setting Sections
			add_settings_section( $slug, $title, array( $this, 'display_section' ), $this->options_slug );
		}

		foreach ( $this->fields as $id => $option ) {
			$option['id'] = $id;
			$this->create_option( $option );
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
		$this->sections['defaults']   = __( 'Defaults', 'plugin-engine' );
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
	public function create_option( $args = array() ) {

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

		$option_args = wp_parse_args( $args, $defaults );

		if ( $option_args['type'] == 'checkbox' ) {
			$this->checkboxes[] = $option_args['id'];
		}

		add_settings_field(
			$option_args['id'],
			$option_args['title'],
			array( 'Pngx__Admin__Fields', 'display_field' ),
			$this->options_slug,
			$option_args['section'],
			$option_args
		);
	}


	/*
	* Coupon Creator Admin Validate Options
	*/
	public function validate_options( $input ) {

		//if Reset is Checked then delete all options
		if ( ! isset( $input['reset_theme'] ) ) {

			//If No CheckBox Sent, then Unset the Option
			$options = get_option( 'coupon_creator_options' );

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
							$permalink_change = $id . "_change";
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
					$license = "cctor_" . $option['class'];

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

					// Remove to not save with Coupon Option Array
					$input[ $id ] = "";
				}

				// Handle License Status
				if ( $option['type'] == 'license_status' ) {
					// Remove to not save with Coupon Option Array
					$input[ $id ] = "";
				}

				// Sanitization Filter for each Option Type
				if ( isset( $input[ $id ] ) && $option['type'] != 'license' && $option['type'] != 'license_status' ) {

					//Send Input to Sanitize Class, will return sanitized input or no input if no sanitization method
					$sanitize = new Pngx__Sanitize( $option['type'], $input[ $id ], $option );

					//Set Sanitized Input in Array
					$clean[ $id ] = $sanitize->result;

				}

			}

			return $clean;
		}

		//Set Option to Flush Permalinks on Next Load as Reset was checked
		update_option( 'cctor_coupon_base_change', true );

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
			'tabs'             => $tabs_array,
			'update_message' => get_settings_errors(),
			'wp_version'            => $wp_version,
		);

		echo '<h1>Coupon Creator:</h1>';

		/**
		 * Before Options Form
		 *
		 */
		do_action( 'pngx_before_option_form' );

		echo '<form action="options.php" method="post">';

		settings_fields( 'coupon_creator_options' );

		echo '<div class="pngx-tabs" ' . Pngx__Admin__Fields::toggle( $tab_data, null ) . '>
						<ul class="pngx-tabs-nav">';

		foreach ( $this->sections as $section_slug => $section ) {
			echo '<li><a href="#' . $section_slug . '">' . $section . '</a></li>';
		}

		echo '</ul>';

		do_settings_sections( $_GET['page'] );

		echo '</div>
					<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes' ) . '" /></p>

				</form>';

		/**
		 * After Coupon Options Forms
		 *
		 *
		 */
		do_action( 'pngx_after_option_form' );

		echo '<p style="text-align:right;">&copy; ' . date( "Y" ) . ' Jessee Productions, LLC</p>';

		echo '</div>';
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

		foreach ( $this->fields as $id => $option ) {

			if ( $option['type'] != 'heading' && isset( $option['std'] ) ) {

				//Sanitize Default
				$cctor_sanitize = new Pngx__Sanitize( $option['type'], $option['std'], $option );

				//Set Sanitized Input in Array
				$default_options[ $id ] = $cctor_sanitize->result;
			}

		}

		update_option( 'coupon_creator_options', $default_options );

	}

	/*
	* Flush Permalink on Coupon Option Change
	*/
	public static function flush_permalinks() {
		if ( get_option( 'cctor_coupon_base_change' ) == true || get_option( 'cctor_coupon_category_base_change' ) == true ) {

			Coupon_Creator_Plugin::cctor_register_post_types();
			flush_rewrite_rules();
			update_option( 'coupon_flush_perm_change', date( 'l jS \of F Y h:i:s A' ) );
			update_option( 'cctor_coupon_base_change', false );
			update_option( 'cctor_coupon_category_base_change', false );
		}
	}


}