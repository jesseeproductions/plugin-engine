<?php

namespace Pngx\Service_Providers;

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
