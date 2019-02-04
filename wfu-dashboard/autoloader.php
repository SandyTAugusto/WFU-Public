<?php

global $wpdb;

$sTableName = $wpdb->prefix . "wfu_resource_links";
$sRegisteredLinksDbVersion = '1.0.0';
$sCharsetCollate = $wpdb->get_charset_collate();

if ($wpdb->get_var("SHOW TABLES LIKE '{$sTableName}'") != $sTableName) {
  $sSqlCall = "CREATE TABLE $sTableName (
    ID mediumint(9) NOT NULL AUTO_INCREMENT,
    `name` text NOT NULL,
    `link` text NOT NULL,
    PRIMARY KEY  (ID)
  ) $sCharsetCollate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sSqlCall);
  
  add_option('my_db_version', $sRegisteredLinksDbVersion );
}

$sTableName = $wpdb->prefix . "wfu_dashboard_messages";
$sRegisteredLinksDbVersion = '1.0.0';
$sCharsetCollate = $wpdb->get_charset_collate();

if ($wpdb->get_var("SHOW TABLES LIKE '{$sTableName}'") != $sTableName) {
  $sSqlCall = "CREATE TABLE $sTableName (
    ID mediumint(9) NOT NULL AUTO_INCREMENT,
    `start` int(11) NOT NULL,
    `end` int(11) NOT NULL,
    `message` text NOT NULL,
    `sites` int(11) NOT NULL DEFAULT 0, #0 will be the global aka all sites
    `users_dismissed` text DEFAULT NULL, #JSON array of users who dismissed
    PRIMARY KEY  (ID)
  ) $sCharsetCollate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sSqlCall);
  
  add_option('my_db_version', $sRegisteredLinksDbVersion );
}

?>