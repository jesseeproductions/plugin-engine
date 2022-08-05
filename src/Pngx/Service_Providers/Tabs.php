<?php

namespace Pngx\Service_Providers;

/**
 * Class Tabs
 *
 * Based off Modern Tribe's Tribe\Service_Providers\Tabs
 *
 * @since TBD
 *
 * Handles the registration and creation of our async process handlers.
 */
class Tabs extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since TBD
	 */
	public function register() {
		pngx_singleton( 'pngx.tabs.view', '\Pngx\Tabs\View' );

		/**
		 * Allows plugins to hook into the register action to register views, etc
		 *
		 * @since TBD
		 *
		 * @param Pngx\Service_Providers\Tabs $tabs
		 */
		do_action( 'pngx_tabs_register', $this );

		$this->hooks();
	}

	/**
	 * Set up hooks for classes.
	 *
	 * @since TBD
	 */
	private function hooks() {
		add_action( 'pngx_engine_loaded', [ $this, 'register_tabs_assets' ] );
		add_filter( 'pngx_template_public_namespace', [ $this, 'template_public_namespace' ], 10, 2 );

		/**
		 * Allows plugins to hook into the hooks action to register their own hooks
		 *
		 * @since TBD
		 *
		 * @param Pngx\Service_Providers\Tabs $tabs
		 */
		do_action( 'pngx_tabs_hooks', $this );
	}

	/**
	  * {@inheritdoc}
	 *
	 * @since  TBD
	 */
	public function template_public_namespace( $namespace, $obj ) {
		if ( ! empty( $obj->template_namespace ) && 'tabs' === $obj->template_namespace ) {
			array_push( $namespace, 'tabs' );
		}

		return $namespace;
	}

	/**
	 * Register assets associated with tabs
	 *
	 * @since TBD
	 */
	public function register_tabs_assets() {
		$main = \Pngx__Main::instance();

/*		pngx_asset(
			$main,
			'pngx-tabs',
			'tabs.css',
			[],
			[],
			[ 'groups' => 'pngx-tabs' ]
		);*/

		pngx_asset(
			$main,
			'A11yTabInterface',
			'vendor/A11yTabInterface/A11yTabInterface.js',
			[],
			[],
			[ 'groups' => 'pngx-tabs' ]
		);

		pngx_asset(
			$main,
			'pngx-tabs-js',
			'tabs.js',
			[ 'a11ytabinterface' ],
			[],
			[ 'groups' => 'pngx-tabs' ]
		);

		/**
		 * Allows plugins to hook into the assets action to register their own assets
		 *
		 * @since TBD
		 *
		 * @param Pngx\Service_Providers\Tabs $tabs
		 */
		do_action( 'pngx_tabs_assets_registered', $this );
	}
}
