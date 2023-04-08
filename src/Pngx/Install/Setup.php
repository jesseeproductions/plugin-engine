<?php
/**
 * Setup the install routines.
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 */

namespace Pngx\Install;

use Pngx__Main as Main;
use Pngx\Traits\With_AJAX;

/**
 * Class Setup
 *
 * @since   4.0.0
 *
 * @package Pngx\Install
 */
class Setup {

	use With_AJAX;

	/**
	 * The name of the transient that will be used to flag whether if setup is active.
	 *
	 * @since 4.0.0
	 */
	public const SETUP_TRANSIENT = 'pngx_setup_active';

	/**
	 * The name of the action used add a coupon to the cart.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	public static $database_install_action = 'pngx-database-reinstall';

	/**
	 * Check version of Plugin Engine database and update.
	 *
	 * @since 4.0.0
	 */
	public function check_version() {
		$pngx_saved_db_version = get_option( 'pngx_db_version' );
		$pngx_db_version       = Main::$db_version;
		$requires_update       = version_compare( $pngx_saved_db_version, $pngx_db_version, '<' );
		if ( $requires_update ) {
			$this->install();
			/**
			 * Run after Plugin Engine has been updated.
			 *
			 * @since 4.0.0
			 */
			do_action( 'pngx_db_updated' );
			// If there is no plugin engine
			if ( ! $pngx_saved_db_version ) {
				/**
				 * Run when plugin engine has been installed for the first time.
				 *
				 * @since 4.0.0
				 */
				do_action( 'pngx_db_newly_installed' );
			}
		}
	}

	/**
	 * Install Plugin Engine Tables.
	 *
	 * @since 4.0.0
	 */
	protected function install() {
		// Check if WordPress database is setup.
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( pngx_is_truthy( get_transient( static::SETUP_TRANSIENT ) ) ) {
			return;
		}

		// Setup transient that the setup is in process.
		set_transient( static::SETUP_TRANSIENT, 'yes', MINUTE_IN_SECONDS * 10 );
		pngx_maybe_define_constant( static::SETUP_TRANSIENT, true );

		pngx( Database::class )::create_tables();
		pngx( Database::class )::verify_base_tables();
		pngx( Cron::class )::create_crons();
		pngx( Database::class )::update_db_version();

		do_action( 'pngx_installing' );

		delete_transient( static::SETUP_TRANSIENT );

		// Set permalink change on next reload of admin.
		update_option( 'pngx_permalink_change', true );

		do_action( 'pngx_installed' );
	}

	/**
	 * Check if the installer is installing.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function is_installing() {
		return pngx_is_truthy( get_transient( static::SETUP_TRANSIENT ) );
	}

	/**
	 * Get the url to install the database.
	 *
	 * Link is only active if the option pngx_database_missing_tables is set to try
	 *
	 * @since 4.0.0
	 *
	 * @return string The url to install the database.
	 */
	public function get_database_install_link() {
		$nonce = wp_create_nonce( static::$database_install_action );
		$query_args = [
			'action'            => static::$database_install_action,
			Main::$request_slug => $nonce,
			'_ajax_nonce'       => $nonce,
		];

		return add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Handles installing the database.
	 *
	 * @since 4.0.0
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 */
	public function ajax_database_install( $nonce ) {
		if ( ! $this->check_ajax_nonce( static::$database_install_action, $nonce ) ) {
			$error_message = _x( 'Incorrect permissions, database install failed.', 'Error message when permissions fail on database install.', 'plugin-engine' );

			wp_die( $error_message );
		}

		$this->install();

		$result = pngx( Database::class )::verify_base_tables( false, false );
		if ( ! empty( $result ) ) {
			$error_message = _x( 'Database install failed. Tables could not be verified.', 'Error message when custom tables could not be verified after database install.', 'plugin-engine' );


			wp_die( $error_message );
		}

		// Redirect back
		if ( ! isset( $_POST['_wp_http_referer'] ) ) {
			$_POST['_wp_http_referer'] = admin_url();
		}

		// Sanitize url and prepare to validate it.
		$url = sanitize_text_field( wp_unslash( $_POST['_wp_http_referer'] ) );
		$location = wp_sanitize_redirect( urldecode( $url ) );

		/**
		 * Filters the redirect fallback URL for when the provided redirect is not safe (local).
		 *
		 * @since 4.3.0
		 *
		 * @param string $fallback_url The fallback URL to use by default.
		 * @param int    $status       The HTTP response status code to use.
		 */
		$fallback_url = apply_filters( 'wp_safe_redirect_fallback', admin_url(), 301 );

		$location = wp_validate_redirect( $location, $fallback_url );

		header( 'refresh:5;url=' . $location );

		wp_die( _x( 'Success! Database custom tables install complete. You will be redirected to the admin in about 5 seconds.', 'Success message when custom tables are installed.', 'plugin-engine' ) );
	}
}