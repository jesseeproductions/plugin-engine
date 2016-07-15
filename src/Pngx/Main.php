<?php
/**
 * Main Plugin Engine class.
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'Pngx__Main' ) ) {
	return;
}


class Pngx__Main {

	const VERSION = '2.3';

	protected $plugin_context;
	protected $plugin_context_class;
	//protected $doing_ajax = false;

	public $plugin_dir;
	public $plugin_path;
	public $plugin_url;

	/**
	 * constructor
	 */
	public function __construct( $context = null ) {

		if ( is_object( $context ) ) {
			$this->plugin_context       = $context;
			$this->plugin_context_class = get_class( $context );
		}

		$this->plugin_path = trailingslashit( dirname( dirname( dirname( __FILE__ ) ) ) );
		$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
		//$this->plugin_url  = plugins_url( $this->plugin_dir );
		//$parent_plugin_dir = trailingslashit( plugin_basename( $this->plugin_path ) );
		//$this->plugin_url  = plugins_url( $parent_plugin_dir === $this->plugin_dir ? $this->plugin_dir : $parent_plugin_dir );

		$this->load_text_domain( 'plugin-engine', basename( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/plugin-engine/lang/' );

		$this->init_autoloading();

		$this->init_libraries();
		$this->add_hooks();

		//$this->doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Get's the instantiated context of this class. I.e. the object that instantiated this one.
	 */
	public function context() {
		return $this->plugin_context;
	}

	/**
	 * Setup the autoloader for common files
	 */
	protected function init_autoloading() {
		if ( ! class_exists( 'Pngx__Autoloader' ) ) {
			require_once dirname( __FILE__ ) . '/Autoloader.php';
		}

		$prefixes   = array( 'Pngx__' => dirname( __FILE__ ) );
		$autoloader = Pngx__Autoloader::instance();
		$autoloader->register_prefixes( $prefixes );
		$autoloader->register_autoloader();
	}

	/**
	 * Get's the class name of the instantiated plugin context of this class. I.e. the class name of the object that
	 * instantiated this one.
	 */
	public function context_class() {
		return $this->plugin_context_class;
	}

	/**
	 * initializes all required libraries
	 */
	public function init_libraries() {
		//Tribe__Debug::instance();
		//Tribe__Settings_Manager::instance();

		//require_once $this->plugin_path . 'src/functions/template-tags/general.php';
		//require_once $this->plugin_path . 'src/functions/template-tags/date.php';

	}

	/**
	 * Registers resources that can/should be enqueued
	 */
	public function load_assets() {
		// These ones are only registred
		/*tribe_assets(
			$this,
			array(
				array( 'tribe-jquery-ui-theme', 'vendor/jquery/ui.theme.css' ),
			)
		);

		// These ones will be enqueued on `admin_enqueue_scripts` if the conditional method on filter is met
		tribe_assets(
			$this,
			array(
				array( 'tribe-common-admin', 'tribe-common-admin.css', array( 'tribe-dependency-style' ) ),
				array( 'tribe-bumpdown', 'bumpdown.js', array( 'jquery', 'underscore', 'hoverIntent' ) ),
				array( 'tribe-dependency', 'dependency.js', array( 'jquery', 'underscore' ) ),
				array( 'tribe-dependency-style', 'dependency.css' ),
				array( 'tribe-notice-dismiss', 'notice-dismiss.js' ),
			),
			'admin_enqueue_scripts',
			array(
				'filter' => array( Tribe__Admin__Helpers::instance(), 'is_post_type_screen' ),
			)
		);*/
	}

	/**
	 * Adds core hooks
	 */
	public function add_hooks() {
		//add_action( 'plugins_loaded', array( 'Tribe__App_Shop', 'instance' ) );
		//add_action( 'plugins_loaded', array( 'Tribe__Assets', 'instance' ), 1 );

		// Register for the assets to be availble everywhere
		//add_action( 'init', array( $this, 'load_assets' ), 1 );
		//add_action( 'plugins_loaded', array( 'Tribe__Admin__Notices', 'instance' ), 1 );
	}

	/**
	 * A Helper method to load text domain
	 * First it tries to load the wp-content/languages translation then if falls to the
	 * try to load $dir language files
	 *
	 * @param string $domain The text domain that will be loaded
	 * @param string $dir    What directory should be used to try to load if the default doenst work
	 *
	 * @return bool  If it was able to load the text domain
	 */
	public function load_text_domain( $domain, $dir = false ) {
		// Added safety just in case this runs twice...
		if ( is_textdomain_loaded( $domain ) && ! is_a( $GLOBALS['l10n'][ $domain ], 'NOOP_Translations' ) ) {
			return true;
		}

		$locale = get_locale();
		$mofile = WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo';

		/**
		 * Allows users to filter which file will be loaded for a given text domain
		 * Be careful when using this filter, it will apply across the whole plugin suite.
		 *
		 * @param string      $mofile The path for the .mo File
		 * @param string      $domain Which plugin domain we are trying to load
		 * @param string      $locale Which Language we will load
		 * @param string|bool $dir    If there was a custom directory passed on the method call
		 */
		$mofile = apply_filters( 'pngx_load_text_domain', $mofile, $domain, $locale, $dir );

		$loaded = load_plugin_textdomain( $domain, false, $mofile );

		if ( $dir !== false && ! $loaded ) {
			return load_plugin_textdomain( $domain, false, $dir );
		}

		return $loaded;
	}

	/**
	 * Returns the post types registered with plugin engine
	 */
	public static function get_post_types() {
		// we default the post type array to empty in tribe-common. Plugins like TEC add to it
		return apply_filters( 'pngx_post_types', array() );
	}

	/**
	 * Insert an array after a specified key within another array.
	 *
	 * @param $key
	 * @param $source_array
	 * @param $insert_array
	 *
	 * @return array
	 *
	 */
	public static function array_insert_after_key( $key, $source_array, $insert_array ) {
		/*if ( array_key_exists( $key, $source_array ) ) {
			$position     = array_search( $key, array_keys( $source_array ) ) + 1;
			$source_array = array_slice( $source_array, 0, $position, true ) + $insert_array + array_slice( $source_array, $position, null, true );
		} else {
			// If no key is found, then add it to the end of the array.
			$source_array += $insert_array;
		}

		return $source_array;*/
	}

	/**
	 * Insert an array immediately before a specified key within another array.
	 *
	 * @param $key
	 * @param $source_array
	 * @param $insert_array
	 *
	 * @return array
	 */
	public static function array_insert_before_key( $key, $source_array, $insert_array ) {
		/*if ( array_key_exists( $key, $source_array ) ) {
			$position     = array_search( $key, array_keys( $source_array ) );
			$source_array = array_slice( $source_array, 0, $position, true ) + $insert_array + array_slice( $source_array, $position, null, true );
		} else {
			// If no key is found, then add it to the end of the array.
			$source_array += $insert_array;
		}

		return $source_array;*/
	}

	/**
	 * Helper function for getting Post Id. Accepts null or a post id. If no $post object exists, returns false to
	 * avoid a PHP NOTICE
	 *
	 * @param int $post (optional)
	 *
	 * @return int post ID or False
	 */
	public static function post_id_helper( $post = null ) {
		/*if ( ! is_null( $post ) && is_numeric( $post ) > 0 ) {
			return (int) $post;
		} elseif ( is_object( $post ) && ! empty( $post->ID ) ) {
			return (int) $post->ID;
		} else {
			if ( ! empty( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof WP_Post ) {
				return get_the_ID();
			} else {
				return false;
			}
		}*/
	}

	/**
	 * Helper function to indicate whether the current execution context is AJAX
	 *
	 * This method exists to allow us test code that behaves differently depending on the execution
	 * context.
	 *
	 * @since 4.0
	 * @return boolean
	 */
	public function doing_ajax( $doing_ajax = null ) {
		/*if ( ! is_null( $doing_ajax ) ) {
			$this->doing_ajax = $doing_ajax;
		}

		return $this->doing_ajax;*/
	}

	/**
	 * Merge the Defaults with new values
	 *
	 * @param $defaults
	 * @param $updates
	 *
	 * @return array
	 */
	public static function merge_defaults( $defaults, $updates ) {

		$updates = (array) $updates;
		$out     = array();
		foreach ( $defaults as $name => $default ) {
			if ( array_key_exists( $name, $updates ) ) {
				$out[ $name ] = $updates[ $name ];
			} else {
				$out[ $name ] = $default;
			}
		}

		return $out;

	}

	/**
	 * Static Singleton Factory Method
	 *
	 * @return Pngx__Main
	 */
	public static function instance() {
		static $instance;

		if ( ! $instance ) {
			$instance = new self;
		}

		return $instance;
	}
}