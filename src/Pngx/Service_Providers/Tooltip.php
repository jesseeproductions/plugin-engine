<?php
namespace Pngx\Service_Providers;

/**
 * Class Tooltip
 *
 * Based off Modern Tribe's Tribe\Service_Providers\Tooltip
 *
 * @since TBD
 *
 * Handles the registration and creation of our async process handlers.
 */
class Tooltip extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since TBD
	 */
	public function register() {
		pngx_singleton( 'tooltip.view', '\Tribe\Tooltip\View' );

		$this->hook();
	}

	/**
	 * Setup hooks for classes.
	 *
	 * @since TBD
	 */
	private function hook() {
		add_action( 'pngx_common_loaded', [ $this, 'add_tooltip_assets' ] );
	}

	/**
	 * Register assets associated with tooltip
	 *
	 * @since TBD
	 */
	public function add_tooltip_assets() {
		$main = \Pngx__Main::instance();

		pngx_asset(
			$main,
			'tribe-tooltip',
			'tooltip.css',
			[ 'tribe-common-style' ],
			[],
			[ 'groups' => 'tribe-tooltip' ]
		);

		pngx_asset(
			$main,
			'tribe-tooltip-js',
			'tooltip.js',
			[ 'jquery', 'tribe-common' ],
			[],
			[ 'groups' => 'tribe-tooltip' ]
		);
	}
}
