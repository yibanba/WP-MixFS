<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * 创建全部工厂表
 */

function create_invoicing_tables($tbl_prefix) {
    global $wpdb;
    $wpdb->hide_errors();
    $collate = '';
    if ($wpdb->has_cap('collation')) {
        if (!empty($wpdb->charset))
            $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
        if (!empty($wpdb->collate))
            $collate .= " COLLATE $wpdb->collate";
    }
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $table_schema_array = [
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_currency_biz` (
            `cb_id` int(9) NOT NULL,
            `cb_date` date NOT NULL,
            `cb_money` decimal(12,2) DEFAULT '0.00',
            `cb_rate` decimal(12,4) NOT NULL DEFAULT '0.0000',
            `cb_summary` varchar(100) DEFAULT NULL,
            `cb_fi_id` int(9) NOT NULL,
            PRIMARY KEY (`cb_id`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_container` (
            `c_id` int(9) NOT NULL AUTO_INCREMENT,
            `c_no` varchar(30) NOT NULL,
            `c_summary` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`c_id`),
            UNIQUE KEY `c_no` (`c_no`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_fee_biz` (
            `fb_id` int(9) NOT NULL AUTO_INCREMENT,
            `fb_date` date NOT NULL,
            `fb_in` decimal(12,2) NOT NULL DEFAULT '0.00',
            `fb_out` decimal(12,2) NOT NULL DEFAULT '0.00',
            `fb_c_id` int(9) DEFAULT '0',
            `fb_summary` varchar(100) DEFAULT NULL,
            `fb_fi_id` int(9) NOT NULL,
            PRIMARY KEY (`fb_id`),
            KEY `fb_c_id` (`fb_c_id`),
            KEY `fb_fi_id` (`fb_fi_id`),
            KEY `fb_date` (`fb_date`),
            KEY `fb_in` (`fb_in`),
            KEY `fb_out` (`fb_out`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_fee_item` (
            `fi_id` int(9) NOT NULL AUTO_INCREMENT,
            `fi_name` varchar(30) NOT NULL,
            `fi_in_out` int(1) NOT NULL,
            `fi_summary` varchar(100) DEFAULT NULL,
            `fi_fs_id` int(9) NOT NULL,
            `fi_order` int(9) NOT NULL DEFAULT '0',
            PRIMARY KEY (`fi_id`),
            KEY `fi_name` (`fi_name`),
            KEY `fi_in_out` (`fi_in_out`),
            KEY `fi_fs_id` (`fi_fs_id`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_fee_series` (
            `fs_id` int(9) NOT NULL AUTO_INCREMENT,
            `fs_name` varchar(30) NOT NULL,
            `fs_summary` varchar(100) DEFAULT NULL,
            `fs_order` int(9) NOT NULL DEFAULT '0',
            PRIMARY KEY (`fs_id`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_goods_biz` (
            `gb_id` int(9) NOT NULL AUTO_INCREMENT,
            `gb_date` date NOT NULL,
            `gb_in` int(9) DEFAULT '0',
            `gb_out` int(9) DEFAULT '0',
            `gb_gn_id` int(9) NOT NULL,
            `gb_num` int(9) NOT NULL,
            `gb_money` decimal(12,2) DEFAULT '0.00',
            `gb_summary` varchar(100) DEFAULT NULL,
            `gb_createdate` datetime DEFAULT '0000-00-00 00:00:00',
            `gb_modifydate` datetime DEFAULT '0000-00-00 00:00:00',
            `gb_userID` int(11) NOT NULL,
            `gb_userIP` varchar(50) NOT NULL,
            PRIMARY KEY (`gb_id`),
            KEY `gb_date` (`gb_date`),
            KEY `gb_gn_id` (`gb_gn_id`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_goods_name` (
            `gn_id` int(9) NOT NULL AUTO_INCREMENT,
            `gn_gs_id` int(9) NOT NULL,
            `gn_name` varchar(30) NOT NULL,
            `gn_price` decimal(12,2) DEFAULT '0.00',
            `gn_summary` varchar(100) DEFAULT NULL,
            `gn_per_pack` int(11) NOT NULL DEFAULT '1',
            PRIMARY KEY (`gn_id`),
            KEY `gn_name` (`gn_name`),
            KEY `gn_gs_id` (`gn_gs_id`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_goods_place` (
            `gp_id` int(9) NOT NULL AUTO_INCREMENT,
            `gp_name` varchar(30) NOT NULL,
            `gp_summary` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`gp_id`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_goods_series` (
            `gs_id` int(9) NOT NULL AUTO_INCREMENT,
            `gs_name` varchar(30) NOT NULL,
            `gs_summary` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`gs_id`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_provider` (
            `p_id` int(9) NOT NULL AUTO_INCREMENT,
            `p_name` varchar(30) NOT NULL,
            `p_summary` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`p_id`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_stuff_biz` (
            `sb_id` int(9) NOT NULL AUTO_INCREMENT,
            `sb_date` date NOT NULL,
            `sb_in` int(9) DEFAULT '0',
            `sb_out` int(9) DEFAULT '0',
            `sb_money` decimal(12,2) DEFAULT '0.00',
            `sb_c_id` int(9) DEFAULT '0',
            `sb_p_id` int(9) DEFAULT '0',
            `sb_summary` varchar(100) DEFAULT NULL,
            `sb_sn_id` int(9) NOT NULL,
            PRIMARY KEY (`sb_id`),
            KEY `sb_date` (`sb_date`),
            KEY `sb_c_id` (`sb_c_id`),
            KEY `sb_p_id` (`sb_p_id`),
            KEY `sb_sn_id` (`sb_sn_id`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_stuff_name` (
            `sn_id` int(9) NOT NULL AUTO_INCREMENT,
            `sn_ss_id` int(9) NOT NULL,
            `sn_name` varchar(30) NOT NULL,
            `sn_price` decimal(12,2) DEFAULT '0.00',
            `sn_summary` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`sn_id`),
            KEY `sn_ss_id` (`sn_ss_id`),
            KEY `sn_name` (`sn_name`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_stuff_series` (
            `ss_id` int(9) NOT NULL AUTO_INCREMENT,
            `ss_name` varchar(30) NOT NULL,
            `ss_summary` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`ss_id`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_currency_biz` (
            `cb_id` int(9) NOT NULL AUTO_INCREMENT,
            `cb_date` date NOT NULL,
            `cb_money` decimal(12,2) DEFAULT '0.00',
            `cb_rate` decimal(12,4) NOT NULL DEFAULT '0.0000',
            `cb_summary` varchar(100) DEFAULT NULL,
            `cb_fi_id` int(9) NOT NULL,
            PRIMARY KEY (`cb_id`)
          ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}___________` (
            `x` int(11) NOT NULL
          ) $collate;"
    ];

    foreach ($table_schema_array as $table) {
        dbDelta($table);
    }
}
