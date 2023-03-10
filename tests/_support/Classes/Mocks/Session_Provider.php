<?php
/**
 * Mock Session Provider
 *
 * By Default Plugin Engine Does not run code controlled by this provider, this is for testing it and can be used as the base in a plugin that uses this feature.
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 */

namespace Pngx\Tests\Classes\Mocks;

use Pngx\Install\Cron;

/**
 * Class Session_Provider
 *
 * @since   4.0.0
 *
 * @package Tribe\Events\Event_Status
 */
class Session_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * The constant to disable the event status coding.
	 *
	 * @since 4.0.0
	 */
	const DISABLED = 'PNGX_SESSIONS_DISABLED';

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.0.0
	 */
	public function register() {
		if ( ! self::is_active() ) {
			return;
		}

		// Register the SP on the container
		$this->container->singleton( 'pngx.session.provider', $this );

		// Register Session Class.
		$this->container->singleton( SessionForTesting::class, SessionForTesting::class );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Returns whether the event status should register, thus activate, or not.
	 *
	 * @since 4.0.0
	 *
	 * @return bool Whether the event status should register or not.
	 */
	public static function is_active() {
		if ( defined( self::DISABLED ) && constant( self::DISABLED ) ) {
			// The disable constant is defined and it's truthy.
			return false;
		}

		if ( getenv( self::DISABLED ) ) {
			// The disable env var is defined and it's truthy.
			return false;
		}

		/**
		 * Allows filtering whether the Sessions Provider should be activated or not.
		 *
		 * Note: this filter will only apply if the disable constant or env var
		 * are not set or are set to falsy values.
		 *
		 * @since 4.0.0
		 *
		 * @param bool $activate Defaults to `true`.
		 */
		return (bool) apply_filters( 'pngx_sessions_enabled', true );
	}

	/**
	 * Adds the actions required for event status.
	 *
	 * @since 4.0.0
	 */
	protected function add_actions() {
		$this->init_session_cookie();
		add_action( Cron::$session_cron_hook, [ $this, 'cleanup_session_data' ] );
		add_action( 'pngx_set_cart_cookies', [ $this, 'set_customer_session_cookie' ], 10 );
		add_action( 'shutdown', [ $this, 'save_data' ], 20 );
		add_action( 'wp_logout', [ $this, 'destroy_session' ] );
		add_action( 'pngx_forget_session', [ $this, 'forget_cookie' ] );
	}

	/**
	 * Initialize the Session.
	 *
	 * @since 4.0.0
	 */
	public function init_session_cookie() {
		$this->container->make( SessionForTesting::class )->init_session_cookie();
	}

	/**
	 * Cleans up session data - cron callback.
	 *
	 * @since 4.0.0
	 */
	public function cleanup_session_data() {
		$session_class = apply_filters( 'pngx_session_handler', 'Pngx_Sessions' );
		$session       = new $session_class();

		if ( is_callable( [ $session, 'cleanup_sessions' ] ) ) {
			$session->cleanup_sessions();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_customer_session_cookie( $set ) {
		return $this->container->make( SessionForTesting::class )->set_customer_session_cookie( $set );
	}

	/**
	 * {@inheritDoc}
	 */
	public function save_data( $logged_out_key ) {
		return $this->container->make( SessionForTesting::class )->save_data( $logged_out_key );
	}

	/**
	 * {@inheritDoc}
	 */
	public function destroy_session( $set ) {
		return $this->container->make( SessionForTesting::class )->destroy_session();
	}

	/**
	 * {@inheritDoc}
	 */
	public function forget_cookie() {
		return $this->container->make( SessionForTesting::class )->forget_cookie();
	}


	/**
	 * Adds the filters required by the plugin.
	 *
	 * @since 4.0.0
	 */
	protected function add_filters() {
		if ( ! is_user_logged_in() ) {
			add_filter( 'nonce_user_logged_out', array( $this, 'maybe_update_nonce_of_logged_out_user' ), 10, 2 );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function maybe_update_nonce_of_logged_out_user( $uid, $action ) {
		return $this->container->make( SessionForTesting::class )->maybe_update_nonce_of_logged_out_user( $uid, $action );
	}
}
