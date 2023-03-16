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

use Pngx\Install\Database;
use Pngx\Install\Setup;
use Pngx\Traits\With_Nonce_Routes;

/**
 * Class Updates
 *
 * @since 4.0.0
 *
 */
class Updates extends \tad_DI52_ServiceProvider {

	use With_Nonce_Routes;

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.0.0
	 */
	public function register() {
		// Register the SP on the container
		$this->container->singleton( 'pngx.updates.provider', $this );

		$this->hook();
		$this->register_database_install();
	}

	/**
	 * Setup hooks for classes.
	 *
	 * @since 4.0.0
	 */
	protected function hook() {
		add_action( 'admin_init', [ $this, 'check_version' ], 5 );
		add_filter( 'pngx_missing_tables_plugin_name', [ $this, 'add_plugin_name' ] );
		add_filter( 'pngx_missing_tables_notice_link', [ $this, 'add_database_install_link' ] );
	}

	/**
	 * Check for Database Setup.
	 *
	 * @since 4.0.0
	 */
	public function check_version() {
		$this->container->make( Setup::class )->check_version();
	}

	/**
	 * Add plugin name to database install notice.
	 *
	 * @since 4.0.0
	 */
	public function add_plugin_name() {
		return 'PNGX';
	}

	/**
	 * Add url database install notice.
	 *
	 * @since 4.0.0
	 */
	public function add_database_install_link() {
		return $this->container->make( Setup::class )->get_database_install_link();
	}

	/**
	 * Register the Database URL only if missing database tables.
	 *
	 * @since 3.4.0
	 */
	protected function register_database_install() {
		$database_missing = get_option( 'pngx_database_missing_tables', false );
		if ( ! $database_missing ) {
			return;
		}

		pngx_notice(
			'pngx_missing_tables',
			[ pngx( Database::class ), 'show_base_tables_missing' ]
		);

		/**
		 * Allows filtering of the capability required to use reinstall the database.
		 *
		 * @since 4.0.0
		 *
		 * @param string $ajax_capability The capability required to use the ajax features, default manage_options.
		 */
		$ajax_capability = apply_filters( 'pngx_database_install_capabilities_check', 'manage_options' );

		$this->route_admin_by_nonce( $this->admin_routes(), $ajax_capability );
	}

	/**
	 * Provides the routes that should be used to handle setup requests.
	 *
	 * The map returned by this method will be used by the `Pngx\Traits\With_Nonce_Routes` trait.
	 *
	 * @since 4.0.0
	 *
	 * @return array<string,callable> A map from the nonce actions to the corresponding handlers.
	 */
	public function admin_routes() {
		$setup = pngx( Setup::class );

		return [
			$setup::$database_install_action => $this->container->callback( Setup::class, 'ajax_database_install' ),
		];
	}
}
