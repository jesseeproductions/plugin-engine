<?php
/**
 * Mock Updates Provider
 *
 * By Default Plugin Engine Does not run code controlled by this provider, this is for testing it and can be used as the base in a plugin that uses this feature.
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 */

namespace Pngx\Tests\Classes\Mocks;

use Pngx\Install\Setup;

/**
 * Class Updates
 *
 * @since 4.0.0
 *
 */
class Updates extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.0.0
	 */
	public function register() {
		// Register the SP on the container
		$this->container->singleton( 'pngx.updates.provider', $this );

		$this->hook();
	}

	/**
	 * Setup hooks for classes.
	 *
	 * @since 4.0.0
	 */
	protected function hook() {
		add_action( 'admin_init', [ $this, 'check_version' ], 5 );
	}

	/**
	 * Check for Database Setup.
	 *
	 * @since 4.0.0
	 */
	public function check_version() {
		$this->container->make( Setup::class )->check_version();
	}
}
