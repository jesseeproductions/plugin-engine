<?php

namespace Pngx\Session;

use Pngx\Install\Setup;
use Pngx\Tests\Traits\With_Uopz;
use Pngx\Tests\Classes\Mocks\SessionForTesting;
use Pngx\Tests\Classes\Mocks\Session_Provider;
use Pngx__Main;

/**
 * Test Pngx Session Abstract Class.
 *
 * @group   core
 *
 * @package Pngx\Session
 */
class SessionTest extends \Codeception\TestCase\WPTestCase {

	use With_Uopz;

	public function setUp() {
		parent::setUp();
		Pngx__Main::instance();
		Setup::install();
		wp_set_current_user( 1 );
		pngx_register_provider( Session_Provider::class );
		$this->handler = pngx( SessionForTesting::class );

		$this->create_session();
	}

	/**
	 * Get session from DB.
	 *
	 * @since 4.0.0
	 *
	 * @param string $session_key The Session key.
	 *
	 * @return array|object|null A session object or null of none.
	 */
	protected function get_session_from_db( $session_key ) {
		global $wpdb;

		$session_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pngx_sessions WHERE `session_key` = %s", $session_key ) );

		return $session_data;
	}

	/**
	 * Helper function to create a WC session and save it to the DB.
	 *
	 * @since 4.0.0
	 *
	 */
	protected function create_session() {
		$this->handler->set( 'cart', 'test cart' );
		$this->handler->save_data();
		$this->session_key  = $this->handler->get_user_id();
		$this->cache_prefix = $this->handler->get_cache_prefix();
	}

	/**
	 * @return SessionForTesting
	 */
	protected function make_instance() {
		return new SessionForTesting( pngx( 'cache' ) );
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
		$session->set_expiration_timestamp();

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
		$session->set_expiration_timestamp();

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

	/**
	 * @test
	 */
	public function test_save_data_should_insert_new_row() {
		$current_session_data = $this->get_session_from_db( $this->session_key );
		// delete session to make sure a new row is created in the DB.
		$this->handler->delete_session( $this->session_key );

		$cache = pngx( 'cache' );
		$this->assertFalse( $cache->get( $this->cache_prefix . $this->session_key, $this->handler->get_cache_group() ) );

		$this->handler->set( 'cart', 'new cart' );
		$this->handler->save_data();

		$updated_session_data = $this->get_session_from_db( $this->session_key );

		$this->assertEquals( $current_session_data->session_id + 1, $updated_session_data->session_id );
		$this->assertEquals( $this->session_key, $updated_session_data->session_key );
		$this->assertEquals( maybe_serialize( [ 'cart' => 'new cart' ] ), $updated_session_data->session_value );
		$this->assertTrue( is_numeric( $updated_session_data->session_expiry ) );
		$this->assertEquals( [ 'cart' => 'new cart' ], $cache->get( $this->cache_prefix . $this->session_key, $this->handler->get_cache_group() ) );
	}

	/**
	 * @test
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
	 * @test
	 */
	public function test_get_session_should_use_cache() {
		$session = $this->handler->get_session( $this->session_key );
		$this->assertEquals( array( 'cart' => 'test cart' ), $session );
	}

	/**
	 * @test
	 */
	public function test_get_session_should_not_use_cache() {
		wp_cache_delete( $this->cache_prefix . $this->session_key, $this->handler->get_cache_group() );
		$session = $this->handler->get_session( $this->session_key );
		$this->assertEquals( array( 'cart' => 'test cart' ), $session );
	}

	/**
	 * @test
	 */
	public function test_get_session_should_return_default_value() {
		$default_session = array( 'session' => 'default' );
		$session         = $this->handler->get_session( 'non-existent key', $default_session );
		$this->assertEquals( $default_session, $session );
	}

	/**
	 * @test
	 */
	public function test_delete_session() {
		global $wpdb;

		$this->handler->delete_session( $this->session_key );

		$session_id = $wpdb->get_var( $wpdb->prepare( "SELECT `session_id` FROM {$wpdb->prefix}pngx_sessions WHERE session_key = %s", $this->session_key ) );

		$this->assertFalse( wp_cache_get( $this->cache_prefix . $this->session_key, $this->handler->get_cache_group() ) );
		$this->assertNull( $session_id );
	}

	/**
	 * @test
	 */
	public function test_update_session_timestamp() {
		global $wpdb;

		$timestamp = 1537970882;

		$this->handler->update_session_timestamp( $this->session_key, $timestamp );

		$session_expiry = $wpdb->get_var( $wpdb->prepare( "SELECT session_expiry FROM {$wpdb->prefix}pngx_sessions WHERE session_key = %s", $this->session_key ) );
		$this->assertEquals( $timestamp, $session_expiry );
	}

	/**
	 * @test
	 */
	public function test_maybe_update_nonce_user_logged_out() {
		$this->assertEquals( 1, $this->handler->maybe_update_nonce_of_logged_out_user( 1, 'wp_rest' ) );
		$this->assertEquals( $this->handler->get_session_id(), $this->handler->maybe_update_nonce_of_logged_out_user( 1, 'pngx-testing' ) );
	}
}
