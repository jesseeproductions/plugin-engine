<?php

namespace Pngx\Install;

use Pngx\Tests\Traits\With_Uopz;


/**
 * Test Pngx Install Scripts
 *
 * @group   core
 *
 * @package Pngx\Install
 */
class DatabaseTest extends \Codeception\TestCase\WPTestCase {

	use With_Uopz;

	/**
	 * @return Database
	 */
	protected function make_instance() {
		return new Database();
	}

	/**
	 * @test
	 */
	public function be_instantiatable() {
		$this->assertInstanceOf( Database::class, $this->make_instance() );
	}

	/**
	 * Integration test for database table creation.
	 *
	 * @group database
	 */
	public function test_create_tables() {
		global $wpdb;

		$Database = $this->make_instance();

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

		$Database::create_tables();
		$result = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}%'" );

		// Check all the tables exist.
		foreach ( $tables as $table ) {
			$this->assertContains( $table, $result );
		}
	}
}
