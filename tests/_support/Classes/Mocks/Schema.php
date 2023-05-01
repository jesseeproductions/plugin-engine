<?php
/**
 * Handles Schema for Mock Database.
 *
 * @since   0.1.0
 *
 * @package Pngx\Tests\Classes\Mocks
 */

namespace Pngx\Tests\Classes\Mocks;

/**
 * Class Schema
 *
 * @since   0.1.0
 *
 * @package Pngx\Tests\Classes\Mocks
 */
class Schema {

	/**
	 * Add the Create Tables Schema.
	 *
	 * @since 0.1.0
	 *
	 * @param string $create_tables The SQL to create tables.
	 *
	 * @return string The SQL to create tables.
	 */
	public function create_table_statements( $create_tables ) {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$add_create_tables = "
			CREATE TABLE {$wpdb->prefix}pngx_sessions (
			  session_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  session_key char(32) NOT NULL,
			  session_value longtext NOT NULL,
			  session_expiry bigint(20) unsigned NOT NULL,
			  PRIMARY KEY  (session_id),
			  UNIQUE KEY session_key (session_key)
			) $collate;

		    CREATE TABLE {$wpdb->prefix}pngx_embeddings (
		        id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
		        uuid varchar(36) NOT NULL,
		        post_id bigint(20),
		        document text NOT NULL,
		        metadata text NOT NULL
		    ) $collate;

			CREATE TABLE {$wpdb->prefix}pngx_embeddings_values (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
				embeddings_id int(11),
				value decimal(14,12)
			) $collate;

		    CREATE TABLE {$wpdb->prefix}pngx_embeddings_terms (
		        id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
		        embeddings_id bigint(20) unsigned NOT NULL,
		        term_id bigint(20) unsigned NOT NULL
		    ) $collate;
		";

		$create_tables .= $add_create_tables;

		return $create_tables;
	}

	/**
	 * Add the Alter Tables Schema.
	 *
	 * @since 0.1.0
	 *
	 * @param string $alter_tables The SQL to alter tables.
	 *
	 * @return string The SQL to alter tables.
	 */
	public function alter_table_statements( $alter_tables ) {
		global $wpdb;

		$add_alter_tables = "
	         ALTER TABLE {$wpdb->prefix}pngx_embeddings_terms
	             ADD CONSTRAINT fk_embeddings_id FOREIGN KEY (embeddings_id) REFERENCES {$wpdb->prefix}pngx_embeddings(id) ON DELETE CASCADE,
	             ADD CONSTRAINT fk_term_id FOREIGN KEY (term_id) REFERENCES {$wpdb->term_taxonomy}(term_id) ON DELETE CASCADE;
	     ";

		$alter_tables .= $add_alter_tables;

		return $alter_tables;
	}

	/**
	 * Add the table names to the array of tables.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string> $tables An array of table names.
	 *
	 * @return array<string> $tables An array of table names.
	 */
	public function table_names( $tables ) {
		global $wpdb;

		$add_tables = [
			"{$wpdb->prefix}pngx_sessions",
			"{$wpdb->prefix}pngx_embeddings",
			"{$wpdb->prefix}pngx_embeddings_values",
			"{$wpdb->prefix}pngx_embeddings_terms",
		];

		$tables = array_merge( $tables, $add_tables );

		return $tables;
	}
}
