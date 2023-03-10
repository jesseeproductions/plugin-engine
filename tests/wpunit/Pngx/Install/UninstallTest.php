<?php

namespace Pngx\Install;

use Pngx\Tests\Traits\With_Uopz;
use Pngx__Main;

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
	public function test_uninstall_file_exists() {
		$exists = file_exists( Pngx__Main::instance()->plugin_path . 'src/Pngx/Install/uninstall.php' );
		$this->assertTrue( $exists );
	}

	/**
	 * @test
	 */
	private function setup_db() {
		global $wpdb;

		$setup = new Setup();
		update_option( 'pngx_db_version', '' );
		delete_transient( 'pngx_setup_active' );

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

	/**
	 * @test
	 */
	public function test_no_uninstall_without_uninstall_flag() {
		global $wpdb;
		$this->setup_db();

		//run uninstall here.
		require Pngx__Main::instance()->plugin_path . 'src/Pngx/Install/uninstall.php';

		$session_cron = wp_get_scheduled_event( Cron::$session_cron_hook );
		$this->assertEquals( $session_cron->hook, Cron::$session_cron_hook  );

		// List of tables created by Database::create_tables.
		$tables = array(
			"{$wpdb->prefix}pngx_sessions",
		);

		$result = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}%'" );
		// Check all the tables exist.
		foreach ( $tables as $table ) {
			$this->assertContains( $table, $result );
		}
	}

	/**
	 * @test
	 */
	public function test_uninstall_with_uninstall_flag() {
		global $wpdb;
		$this->setup_db();

		//run uninstall here.
		defined( 'PNGX_UNINSTALL_PLUGIN' ) ? '' : define( 'PNGX_UNINSTALL_PLUGIN', true );
		require Pngx__Main::instance()->plugin_path . 'src/Pngx/Install/uninstall.php';

		$session_cron = wp_get_scheduled_event( Cron::$session_cron_hook );
		$this->assertFalse( $session_cron );

		// List of tables created by Database::create_tables.
		$tables = array(
			"{$wpdb->prefix}pngx_sessions",
		);

		$result = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}%'" );
		// Check all the tables exist.
		foreach ( $tables as $table ) {
			$this->assertContains( $table, $result );
		}
	}

	/**
	 * @test
	 */
	public function test_uninstall_with_uninstall_flag_and_remove_all_data_flag() {
		global $wpdb;
		$this->setup_db();

		//run uninstall here.
		defined( 'PNGX_UNINSTALL_PLUGIN' ) ? '' : define( 'PNGX_UNINSTALL_PLUGIN', true );
		defined( 'PNGX_REMOVE_ALL_DATA' ) ? '' : define( 'PNGX_REMOVE_ALL_DATA', true );
		require Pngx__Main::instance()->plugin_path . 'src/Pngx/Install/uninstall.php';

		$session_cron = wp_get_scheduled_event( Cron::$session_cron_hook );
		$this->assertFalse( $session_cron );

		// List of tables created by Database::create_tables.
		$tables = array(
			"{$wpdb->prefix}pngx_sessions",
		);

		$result = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}%'" );
		// Check all the tables exist.
		foreach ( $tables as $table ) {
			$this->assertNotContains( $table, $result );
		}
	}
}
