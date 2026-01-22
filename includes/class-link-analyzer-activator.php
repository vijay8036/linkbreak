<?php
/**
 * Fired during plugin activation
 */

class Link_Analyzer_Activator {

	/**
	 * Create the custom database table for storing URLs.
	 */
	public static function activate() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'link_analyzer_urls';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			url text NOT NULL,
			status_code int(5) DEFAULT 0,
			status_label varchar(50) DEFAULT 'unknown',
			source_type varchar(50) DEFAULT '',
			source_id bigint(20) DEFAULT 0,
			source_name varchar(255) DEFAULT '',
			context_snippet text DEFAULT '',
			last_scanned datetime DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id),
			KEY status_code (status_code)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}
