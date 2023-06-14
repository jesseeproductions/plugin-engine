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

		/*
		 * Using WordPress value and note from WordPress: Indexes have a maximum size of 767 bytes. Historically, we haven't need to be concerned about that.
		 * As of 4.2, however, we moved to utf8mb4, which uses 4 bytes per character. This means that an index which
		 * used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
		 */
		$max_index_length = 191;

		$add_create_tables = "
CREATE TABLE {$wpdb->prefix}pngx_embeddings_meta (
	meta_id bigint(20) unsigned NOT NULL auto_increment,
	embeddings_id bigint(20) unsigned NOT NULL default '0',
	meta_key varchar(255) default NULL,
	meta_value longtext,
	PRIMARY KEY  (meta_id),
	KEY embeddings_id (embeddings_id),
	KEY meta_key (meta_key($max_index_length))
) $collate;
CREATE TABLE {$wpdb->prefix}pngx_embeddings_chunks (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    uuid varchar(36) NOT NULL,
    embeddings_date datetime NOT NULL default '0000-00-00 00:00:00',
	embeddings_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
    embeddings_post_id bigint(20),
    content_id bigint(20),
    content_date datetime NOT NULL default '0000-00-00 00:00:00',
    document text NOT NULL,
    PRIMARY KEY  (id),
	KEY type_status_date (embeddings_post_id,content_id,embeddings_date,id)
) $collate;
CREATE TABLE {$wpdb->prefix}pngx_embeddings_values (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	embeddings_id int(11),
	value decimal(14,12),
	KEY embeddings_id (embeddings_id)
) $collate;
CREATE TABLE {$wpdb->prefix}pngx_embeddings_terms (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    embeddings_id bigint(20) unsigned NOT NULL,
    term_id bigint(20) unsigned NOT NULL,
    KEY embeddings_id (embeddings_id),
    KEY term_id (term_id)
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
	 * @param array $alter_tables An array of SQL to alter tables.
	 *
	 * @return array An array of SQL to alter tables.
	 */
	public function alter_table_statements( $alter_tables ) {
		global $wpdb;

		$alter_tables[] = "ALTER TABLE {$wpdb->prefix}pngx_embeddings_terms
		         ADD CONSTRAINT fk_embeddings_id FOREIGN KEY (embeddings_id) REFERENCES {$wpdb->prefix}pngx_embeddings_chunks(id) ON DELETE CASCADE,
		         ADD CONSTRAINT fk_term_id FOREIGN KEY (term_id) REFERENCES {$wpdb->term_taxonomy}(term_id) ON DELETE CASCADE;";

		$alter_tables[] = "ALTER TABLE {$wpdb->prefix}pngx_embeddings_meta
				    ADD CONSTRAINT fk_meta_embeddings_id FOREIGN KEY (embeddings_id) REFERENCES {$wpdb->prefix}pngx_embeddings_chunks(id) ON DELETE CASCADE";

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
			"{$wpdb->prefix}pngx_embeddings_meta",
			"{$wpdb->prefix}pngx_embeddings_chunks",
			"{$wpdb->prefix}pngx_embeddings_values",
			"{$wpdb->prefix}pngx_embeddings_terms",
		];

		$tables = array_merge( $tables, $add_tables );

		return $tables;
	}
}
