<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Add Cron Schedule
 *
 *
 */
class Pngx__Cron_20 {

	/**
	 * Add filters to register custom cron schedules
	 *
	 * @return void
	 */
	public function filter_cron_schedules() {
		add_filter( 'cron_schedules', array( $this, 'register_20min_interval' ) );
	}

	/**
	 * Add a new scheduled task interval (of 20mins).
	 *
	 * @param  array $schedules
	 *
	 * @return array
	 */
	public function register_20min_interval( $schedules ) {

		$schedules['every_20mins'] = array(
			'interval' => 20 * MINUTE_IN_SECONDS,
			'display'  => __( 'Once Every 20 Mins', 'plugin-engine' ),
		);

		return $schedules;
	}

}