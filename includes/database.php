<?php

/*
* Creamos la tabla necesaria para el plugin
*/
global $ninja_seo_links_db_version;
$ninja_seo_links_db_version = '1.0';

function ninja_seo_links_charset() {
	global $wpdb;

	$charset_collate = '';
	if ( ! empty( $wpdb->charset ) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

	if ( ! empty( $wpdb->collate ) )
		$charset_collate .= " COLLATE $wpdb->collate";

	return $charset_collate;
}

function ninja_seo_links_install() {
	global $wpdb;
	global $ninja_seo_links_db_version;

	$table_name = $wpdb->prefix . "ninja_seo_links";

	$charset_collate = ninja_seo_links_charset();

	$sql = "CREATE TABLE $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  origin varchar(200) DEFAULT '' NOT NULL,
	  word varchar(200) DEFAULT '' NOT NULL,
	  destination varchar(200) DEFAULT '' NOT NULL,
	  title varchar(200) DEFAULT '' NOT NULL,
	  type varchar(60) DEFAULT '' NOT NULL,
	  open varchar(60) DEFAULT '' NOT NULL,
	  created int(11) NOT NULL,
	  hits smallint(5) NOT NULL,
	  active smallint(5) NOT NULL,
	  PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'ninja_seo_links_db_version', $ninja_seo_links_db_version );
}
