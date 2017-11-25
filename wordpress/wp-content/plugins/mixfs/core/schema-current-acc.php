<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


/**
 * 创建全部往来账表
 */

function create_current_acc_tables($tbl_prefix) {
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
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_biz` (
            `bid` int(11) NOT NULL AUTO_INCREMENT,
            `bdate` date NOT NULL,
            `bcid` int(5) NOT NULL DEFAULT '0',
            `biid` int(3) NOT NULL DEFAULT '0',
            `bdollar` decimal(12,2) DEFAULT '0.00',
            `buah` decimal(12,2) DEFAULT '0.00',
            `bcontainer` varchar(20) DEFAULT '0',
            `bsummary` varchar(200) DEFAULT NULL,
            `bconfirm` tinyint(1) NOT NULL DEFAULT '0',
            `brelate` int(7) DEFAULT '0',
            `bshow` tinyint(1) NOT NULL DEFAULT '1',
            PRIMARY KEY (`bid`),
            KEY `bdate` (`bdate`),
            KEY `bcid` (`bcid`),
            KEY `biid` (`biid`),
            KEY `bcontainer` (`bcontainer`)
        ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_client` (
            `cid` int(11) NOT NULL AUTO_INCREMENT,
            `cdate` date NOT NULL,
            `cname` varchar(20) NOT NULL,
            `ctel` varchar(20) DEFAULT NULL,
            `caddr` varchar(100) DEFAULT NULL,
            `csummary` varchar(200) DEFAULT NULL,
            PRIMARY KEY (`cid`)
        ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_item` (
            `iid` int(11) NOT NULL AUTO_INCREMENT,
            `ititle` varchar(20) NOT NULL,
            `iinout` int(1) NOT NULL DEFAULT '0',
            `isummary` varchar(200) DEFAULT NULL,
            PRIMARY KEY (`iid`),
            KEY `iinout` (`iinout`)
        ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}___________` (
            `x` int(11) NOT NULL
        ) $collate;"
    ];

    foreach ($table_schema_array as $table) {
        dbDelta($table);
    }
}
