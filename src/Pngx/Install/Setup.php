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

/**
 * Class Setup
 *
 * @since   4.0.0
 *
 * @package Pngx\Install
 */
class Setup {

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

	public function install() {
		// Check if WordPress database is setup.
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( pngx_is_truthy( get_transient( 'pngx_setup_active' ) ) ) {
			return;
		}

		// Setup transient that the setup is in process.
		set_transient( 'pngx_setup_active', 'yes', MINUTE_IN_SECONDS * 10 );
		pngx_maybe_define_constant( 'pngx_setup_active', true );

		pngx( Database::class )::create_tables();
		pngx( Database::class )::verify_base_tables();
		pngx( Cron::class )::create_crons();
		pngx( Database::class )::update_db_version();

		do_action( 'pngx_installing' );

		delete_transient( 'pngx_setup_active' );

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
		return pngx_is_truthy( get_transient( 'pngx_setup_active' ) );
	}
}