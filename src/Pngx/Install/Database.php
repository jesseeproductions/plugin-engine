<?php
/**
 * Custom Database Setup.
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 */

namespace Pngx\Install;

use Pngx__Main;

/**
 * Class Database
 *
 * @since   4.0.0
 *
 * @package Pngx\Install
 */
class Database  {

	/**
	 * Create custom tables for Plugin Engine.
	 *
	 * @since 4.0.0
	 */
	public static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// dbDelta() cannot handle primary key changes, if there are changes to a primary key, run them here before it.
		$create_tables = self::get_schema();
		if ( ! empty( $create_tables ) ) {
			dbDelta( $create_tables );
		}
	}

	/**
	 * Alter tables.
	 *
	 * @since 4.0.0
	 */
	public static function alter_tables() {
		global $wpdb;

		$alter_tables = self::get_alter_schema();

		if ( ! empty( $alter_tables ) ) {
			$wpdb->query( $alter_tables );
		}
	}

	/**
	 * Get custom table schema.
	 *
	 * @since 4.0.0
	 *
	 * Add or remove the table from Install::get_tables().
	 *
	 * @return string The sql to create or update tables.
	 */
	private static function get_schema() {
		$create_tables = "";

		/**
		 * Filter the create table schema.
		 *
		 * @since 4.0.0
		 *
		 * @param string $create_tables The SQL to create tables.
		 */
		$tables = (string) apply_filters('pngx_create_table_statements', $create_tables );

        return $tables;
	}

	/**
	 * Get alter table schema.
	 *
	 * @since 4.0.0
	 *
	 * @return string The SQL to alter tables.
	 */
	private static function get_alter_schema() {
		$alter_tables = "";

		/**
		 * Filter the alter table schema.
		 *
		 * @since 4.0.0
		 *
		 * @param string $alter_tables The SQL to alter tables.
		 */
		$alter_tables = (string) apply_filters('pngx_alter_table_statements', $alter_tables );

        return $alter_tables;
	}

	/**
	 * Get a list of Plugin Engine table names.
	 *
	 * @since 4.0.0
	 *
	 * @return array<int|string> $tables An array of Plugin Engine table names.
	 */
	public static function get_tables() {
		$tables = [];

		/**
		 * Filter the list of Plugin Engine table names.
		 *
		 * @since 4.0.0
		 *
		 * @param array<int|string> $tables An array of Plugin Engine table names.
		 */
		$tables = apply_filters( 'pngx_install_get_tables', $tables );

		return $tables;
	}

	/**
	 * Check if custom tables are created.
	 *
	 * @param bool $modify_notice Whether to modify notice based on if all tables are present.
	 * @param bool $execute       Whether to execute get_schema queries as well.
	 *
	 * @return array<string> List of queries.
	 */
	public static function verify_base_tables( $modify_notice = true, $execute = false ) {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		if ( $execute ) {
			self::create_tables();
		}

		$queries = [];
		$create_tables = self::get_schema();
		if ( ! empty( $create_tables ) ) {
			$queries = dbDelta( $create_tables, false );
		}
		$missing_tables = [];
		foreach ( $queries as $table_name => $result ) {
			if ( "Created table $table_name" === $result ) {
				$missing_tables[] = $table_name;
			}
		}

		if ( 0 < count( $missing_tables ) ) {
			if ( $modify_notice ) {
				pngx_notice(
					'pngx_missing_tables',
					[ pngx( Database::class ), 'show_base_tables_missing' ]
				);
			}

			update_option( 'pngx_schema_missing_tables', $missing_tables );
		} else {
			if ( $modify_notice ) {
				\Pngx__Admin__Notices::instance()->remove( 'pngx_missing_tables' );
			}

			update_option( Pngx__Main::$db_version_key, Pngx__Main::$db_version );
			update_option( 'pngx_database_missing_tables', false );
			delete_option( 'pngx_schema_missing_tables', [] );
		}
		return $missing_tables;
	}

	/**
	 * Drop tables.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string> $tables An array of table names.
	 */
	public static function drop_tables( $tables ) {
		global $wpdb;

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		}
	}

	/**
	 * Show Base Table Missing Notice.
	 *
	 * @since 4.0.0
	 */
	public function show_base_tables_missing() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		/**
		 * Filter the plugin name for missing tables notice to be able to reinstall.
		 *
		 * @since 4.0.0
		 *
		 * @param string The default plugin name is Plugin Engine.
		 */
		$plugin_name = apply_filters( 'pngx_missing_tables_plugin_name', 'Plugin Engine' );

		/**
		 * Filter the url for missing tables notice to be able to reinstall.
		 *
		 * @since 4.0.0
		 *
		 * @param string The default link, empty string as it must be provided by a plugin.
		 */
		$database_install_link = apply_filters( 'pngx_missing_tables_notice_link', '' );
		if ( empty( $database_install_link) ) {
			return;
		}

		$missing_tables = get_option( 'pngx_schema_missing_tables', [] );

		printf(
			'<div class="error pngx-notice pngx-dependency-error" data-plugin="%1$s"><p>'
			. _x( 'One or more custom tables are missing from your install of %2$s. Missing tables: %3$s. <a href="%4$s">Run install again with this link.</a>', 'Error message that displays if missing custom tables, it provides a link to install tables again.', 'plugin-engine' )
			. '</p></div>',
			'plugin-engine',
			$plugin_name,
			implode(", ", $missing_tables ),
			pngx_sanitize_url( $database_install_link )
		);
	}

	/**
	 * Update DB version to current.
	 *
	 * @since 4.0.0
	 *
	 * @param string|null $version New Plugin Engine DB version or null.
	 */
	public static function update_db_version( $version = null ) {
		update_option( 'pngx_db_version', is_null( $version ) ? Pngx__Main::$db_version : $version );
	}
}