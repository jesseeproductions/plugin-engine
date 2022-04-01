<?php
/**
 * Session Provider
 *
 * @since   TBD
 *
 * @package Pngx\Session
 */

namespace Pngx\Session;

/**
 * Class Session_Provider
 *
 * @since   TBD
 *
 * @package Tribe\Events\Event_Status
 */
class Session_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since TBD
	 */
	public function register() {
		if ( ! self::is_active() ) {
			return;
		}

		// Register the SP on the container
		$this->container->singleton( 'pngx.session.provider', $this );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required for event status.
	 *
	 * @since TBD
	 */
	protected function add_actions() {

		add_action( 'pngx_cleanup_sessions', [ $this, 'cleanup_session_data' ] );

		// todo these need to be connected into the main class
		//add_action( 'woocommerce_cleanup_sessions', [ $this, 'cleanup_session_data' ] );
		//add_action( 'woocommerce_set_cart_cookies', array( $this, 'set_customer_session_cookie' ), 10 );
		//add_action( 'shutdown', array( $this, 'save_data' ), 20 );
		//add_action( 'wp_logout', array( $this, 'destroy_session' ) );


		//add_action( 'pngx_forget_session'. [ $this, 'forget_cookie' ] );
	}

	/**
	 * Adds the filters required by the plugin.
	 *
	 * @since TBD
	 */
	protected function add_filters() {

		if ( ! is_user_logged_in() ) {
			add_filter( 'nonce_user_logged_out', array( $this, 'maybe_update_nonce_of_logged_out_user' ), 10, 2 );
		}
	}

	/**
	 * Add the status classes for the views v2 elements
	 *
	 * @since 5.11.0
	 *
	 * @param array<string|string> $classes Space-separated string or array of class names to add to the class list.
	 * @param int|WP_Post          $post    Post ID or post object.
	 *
	 * @return array<string|string> An array of post classes with the status added.
	 */
	public function filter_add_post_class( $classes, $class, $post ) {
		$new_classes = $this->container->make( Template_Modifications::class )->get_post_classes( $post );

		return array_merge( $classes, $new_classes );
	}

	/**
	 * Cleans up session data - cron callback.
	 * //todo add this on install of upgrade.
	 *
	 * @since TBD
	 */
	public function cleanup_session_data() {
		$session_class = apply_filters( 'pngx_session_handler', 'Pngx_Sessions' );
		$session       = new $session_class();

		if ( is_callable( [ $session, 'cleanup_sessions' ] ) ) {
			$session->cleanup_sessions();
		}
	}


}
