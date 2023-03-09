<?php
/**
 * Setup the install routines..
 *
 * @since   TBD
 *
 * @package Pngx\Session
 */

namespace Pngx\Install;

/**
 * Class Setup
 *
 * @since   TBD
 *
 * @package Pngx\Install
 */
class Setup  {

	public static function install() {
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
		pngx( Cron::class )::create_cron_jobs();

		//todo

		do_action( 'pngx_installing' );

		delete_transient( 'pngx_setup_active' );

		// Set permalink change on next reload of admin.
		update_option( 'pngx_permalink_change', true );

		do_action( 'pngx_installed' );
	}


	/**
	 * Check if the installer is installing.
	 *
	 * @return bool
	 */
	public static function is_installing() {
		return pngx_is_truthy( get_transient( 'pngx_setup_active' ) );
	}
}