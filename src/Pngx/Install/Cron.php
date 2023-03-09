<?php
/**
 * Cron Setup.
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 */

namespace Pngx\Install;

/**
 * Class Cron
 *
 * @since   4.0.0
 *
 * @package Pngx\Install
 */
class Cron  {

	/**
	 * Create crons for Plugin Engine.
	 *
	 * @since 4.0.0
	 */
	public static function create_crons() {
		// Clear cron jobs to reduce conflict.
		wp_clear_scheduled_hook( 'pngx_cleanup_sessions' );

		//Setup crons.
		wp_schedule_event( time() + ( 6 * HOUR_IN_SECONDS ), 'twicedaily', 'pngx_cleanup_sessions' );
	}
}