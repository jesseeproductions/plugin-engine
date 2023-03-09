<?php

namespace Pngx\Install;

use Pngx\Tests\Traits\With_Uopz;

/**
 * Test Pngx Setup Scripts
 *
 * @group   core
 *
 * @package Pngx\Install
 */
class UninstallTest extends \Codeception\TestCase\WPTestCase {

	use With_Uopz;

	/**
	 * @return Setup
	 */
	protected function make_instance() {
		return new Setup();
	}

	/**
	 * @test
	 */
	public function be_instantiatable() {
		$this->assertInstanceOf( Setup::class, $this->make_instance() );
	}

	/**
	 * Integration test for database table creation.
	 *
	 * @group database
	 */
	public function test_install_on_db_change() {
		global $wpdb;

		$setup = $this->make_instance();
		update_option( 'pngx_db_version', '' );

		// Remove the Test Suite’s use of temporary tables https://wordpress.stackexchange.com/a/220308.
		remove_filter( 'query', array( $this, '_create_temporary_tables' ) );
		remove_filter( 'query', array( $this, '_drop_temporary_tables' ) );

		// List of tables created by Database::create_tables.
		$tables = array(
			"{$wpdb->prefix}pngx_sessions",
		);

		// Remove any existing tables in the environment.
		$query = 'DROP TABLE IF EXISTS ' . implode( ',', $tables );
		$wpdb->query( $query ); // phpcs:ignore.

		$pngx_saved_db_version = get_option( 'pngx_db_version' );
		$this->assertEmpty( $pngx_saved_db_version );

		$setup->check_version();

		$result = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}%'" );
		// Check all the tables exist.
		foreach ( $tables as $table ) {
			$this->assertContains( $table, $result );
		}

		$pngx_saved_db_version = get_option( 'pngx_db_version' );
		$pngx_db_version       = \Pngx__Main::$db_version;
		$this->assertEquals( $pngx_saved_db_version, $pngx_db_version );
	}
}
