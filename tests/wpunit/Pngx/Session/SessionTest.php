<?php

namespace Pngx\Session;

use Pngx\Tests\Traits\With_Uopz;

if ( ! class_exists( '\\SessionForTesting' ) ) {
	require_once codecept_data_dir( 'classes/mocks/SessionForTesting.php' );
}

/**
 * Test Pngx Session Abstract Class.
 *
 * @group   core
 *
 * @package Cctor__Coupon__Main
 */
class SessionTest extends \Codeception\TestCase\WPTestCase {

	use With_Uopz;

	/**
	 * @return SessionForTesting
	 */
	protected function make_instance() {
		return new SessionForTesting();
	}

	/**
	 * @test
	 */
	public function be_instantiatable() {
		$this->assertInstanceOf( SessionForTesting::class, $this->make_instance() );
	}

	/**
	 * @test
	 */
	public function it_has_default_cooke_name() {
		$session = $this->make_instance();

		$this->assertEquals( 'pngx_session_' . COOKIEHASH, $session->get_cookie_name() );
	}

	/**
	 * @test
	 */
	public function it_has_default_table_name() {
		$session = $this->make_instance();

		$this->assertEquals( $GLOBALS['wpdb']->prefix . 'pngx_sessions', $session->get_table_name() );
	}

	/**
	 * @test
	 */
	public function it_can_use_magic_method_to_set_and_get() {
		$session = $this->make_instance();
		$key     = 'test-key';

		$session->__set( $key, 'test value' );

		$this->assertEquals( 'test value', $session->__get( $key ) );
		$this->assertTrue( $session->__isset( $key ) );
	}

	/**
	 * @test
	 */
	public function it_can_use_magic_method_to_unset() {
		$session = $this->make_instance();
		// Test with invalid key that will be sanitized to valid.
		$key = 'test key';
		// Unset utilizes the exact key with sanitization to prevent someone from unsetting the wrong value.
		$unsetkey = 'testkey';

		$session->__set( $key, 'test value' );

		$this->assertEquals( 'test value', $session->__get( $key ) );
		$this->assertTrue( $session->__isset( $key ) );

		$session->__unset( $unsetkey );

		$this->assertNull( $session->__get( $key ) );
		$this->assertFalse( $session->__isset( $key ) );
	}

	/**
	 * @test
	 */
	public function it_can_use_method_to_set_and_get() {
		$session = $this->make_instance();
		$key     = 'test-key';

		$session->set( $key, 'test value' );

		$this->assertEquals( 'test value', $session->get( $key ) );
		$this->assertTrue( $session->__isset( $key ) );
	}

	/**
	 * @test
	 */
	public function it_sets_session_expiration_dates_correctly() {
		$this->uopz_set_return( 'time', '1637837732' );
		$session = $this->make_instance();
		$now     = time();
		$session->set_session_expiration();

		$this->assertEquals( $now + HOUR_IN_SECONDS * 48, $session->get_expiration_timestamp() );
		$this->assertEquals( $now + HOUR_IN_SECONDS * 47, $session->get_expiring_soon_timestamp() );
	}

	/**
	 * @test
	 */
	public function it_sets_session_expiration_dates_correctly_when_filtered() {
		$this->uopz_set_return( 'time', '1637837732' );
		$session = $this->make_instance();
		$now     = time();
		add_filter( 'pngx_expiration_timestamp', static function ( $expiration_timestamp ) use ( $now ) {
			return HOUR_IN_SECONDS * 20;
		}, 10 );
		add_filter( 'pngx_expiring_soon_timestamp', static function ( $expiring_soon_timestamp ) use ( $now ) {
			return HOUR_IN_SECONDS * 19;
		}, 10 );
		$session->set_session_expiration();

		$this->assertEquals( $now + HOUR_IN_SECONDS * 20, $session->get_expiration_timestamp() );
		$this->assertEquals( $now + HOUR_IN_SECONDS * 19, $session->get_expiring_soon_timestamp() );
	}

	public function user_states() {
		return [
			[ true, 65 ],
			[ false, 40 ],
		];
	}
	/**
	 * @test
	 * @dataProvider  user_states
	 */
	public function it_generates_correct_unique_id( $logged_in, $user_id ) {
		$this->uopz_set_return( 'is_user_logged_in', $logged_in );
		$this->uopz_set_return( 'get_current_user_id', $user_id );
		$session = $this->make_instance();
		$session->generate_unique_id();

		if ( $logged_in ) {
			$this->assertEquals( $user_id, $session->get_session_id() );
		}

		if ( ! $logged_in ) {
			$this->assertNotEquals( $user_id, $session->get_session_id() );
		}
	}

	// todo add install checks for database
	// todo test get_cache_prefix - cache system needs to be setup does woocommerce have caching tests?
	// todo test get_user_id
	// todo test saving dnd get_session_data
	// todo test deleting, forgetting
	// todo test cookies, saving, deleting, updating
	// todo test maybe_update_nonce_of_logged_out_user - can I do this with a logged out user?
	// todo test cron is setup and running


	// todo change these to work with my coding:
/*	public function setUp() {
		parent::setUp();

		$this->handler = new WC_Session_Handler();
		$this->create_session();
	}*/

	/**
	 * @testdox Test that save data should insert new row.
	 */
	public function test_save_data_should_insert_new_row() {
		$current_session_data = $this->get_session_from_db( $this->session_key );
		// delete session to make sure a new row is created in the DB.
		$this->handler->delete_session( $this->session_key );
		$this->assertFalse( wp_cache_get( $this->cache_prefix . $this->session_key, WC_SESSION_CACHE_GROUP ) );

		$this->handler->set( 'cart', 'new cart' );
		$this->handler->save_data();

		$updated_session_data = $this->get_session_from_db( $this->session_key );

		$this->assertEquals( $current_session_data->session_id + 1, $updated_session_data->session_id );
		$this->assertEquals( $this->session_key, $updated_session_data->session_key );
		$this->assertEquals( maybe_serialize( array( 'cart' => 'new cart' ) ), $updated_session_data->session_value );
		$this->assertTrue( is_numeric( $updated_session_data->session_expiry ) );
		$this->assertEquals( array( 'cart' => 'new cart' ), wp_cache_get( $this->cache_prefix . $this->session_key, WC_SESSION_CACHE_GROUP ) );
	}

	/**
	 * @testdox Test that save data should replace existing row.
	 */
	public function test_save_data_should_replace_existing_row() {
		$current_session_data = $this->get_session_from_db( $this->session_key );

		$this->handler->set( 'cart', 'new cart' );
		$this->handler->save_data();

		$updated_session_data = $this->get_session_from_db( $this->session_key );

		$this->assertEquals( $current_session_data->session_id, $updated_session_data->session_id );
		$this->assertEquals( $this->session_key, $updated_session_data->session_key );
		$this->assertEquals( maybe_serialize( array( 'cart' => 'new cart' ) ), $updated_session_data->session_value );
		$this->assertTrue( is_numeric( $updated_session_data->session_expiry ) );
	}

	/**
	 * @testdox Test that get_setting() should use cache.
	 */
	public function test_get_session_should_use_cache() {
		$session = $this->handler->get_session( $this->session_key );
		$this->assertEquals( array( 'cart' => 'fake cart' ), $session );
	}

	/**
	 * @testdox Test that get_setting() shouldn't use cache.
	 */
	public function test_get_session_should_not_use_cache() {
		wp_cache_delete( $this->cache_prefix . $this->session_key, WC_SESSION_CACHE_GROUP );
		$session = $this->handler->get_session( $this->session_key );
		$this->assertEquals( array( 'cart' => 'fake cart' ), $session );
	}

	/**
	 * @testdox Test that get_setting() should return default value.
	 */
	public function test_get_session_should_return_default_value() {
		$default_session = array( 'session' => 'default' );
		$session         = $this->handler->get_session( 'non-existent key', $default_session );
		$this->assertEquals( $default_session, $session );
	}

	/**
	 * @testdox Test delete_session().
	 */
	public function test_delete_session() {
		global $wpdb;

		$this->handler->delete_session( $this->session_key );

		$session_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `session_id` FROM {$wpdb->prefix}woocommerce_sessions WHERE session_key = %s",
				$this->session_key
			)
		);

		$this->assertFalse( wp_cache_get( $this->cache_prefix . $this->session_key, WC_SESSION_CACHE_GROUP ) );
		$this->assertNull( $session_id );
	}

	/**
	 * @testdox Test update_session_timestamp().
	 */
	public function test_update_session_timestamp() {
		global $wpdb;

		$timestamp = 1537970882;

		$this->handler->update_session_timestamp( $this->session_key, $timestamp );

		$session_expiry = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT session_expiry FROM {$wpdb->prefix}woocommerce_sessions WHERE session_key = %s",
				$this->session_key
			)
		);
		$this->assertEquals( $timestamp, $session_expiry );
	}

	/**
	 * @testdox Test that nonce of user logged out is only changed by WooCommerce.
	 */
	public function test_maybe_update_nonce_user_logged_out() {
		$this->assertEquals( 1, $this->handler->maybe_update_nonce_user_logged_out( 1, 'wp_rest' ) );
		$this->assertEquals( $this->handler->get_customer_unique_id(), $this->handler->maybe_update_nonce_user_logged_out( 1, 'woocommerce-something' ) );
	}

	/**
	 * Helper function to create a WC session and save it to the DB.
	 */
	protected function create_session() {
		$this->handler->init();
		wp_set_current_user( 1 );
		$this->handler->set( 'cart', 'fake cart' );
		$this->handler->save_data();
		$this->session_key  = $this->handler->get_customer_id();
		$this->cache_prefix = WC_Cache_Helper::get_cache_prefix( WC_SESSION_CACHE_GROUP );
	}

	/**
	 * Helper function to get session data from DB.
	 *
	 * @param string $session_key Session key.
	 * @return stdClass
	 */
	protected function get_session_from_db( $session_key ) {
		global $wpdb;

		$session_data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}woocommerce_sessions WHERE `session_key` = %s",
				$session_key
			)
		);

		return $session_data;
	}
}
