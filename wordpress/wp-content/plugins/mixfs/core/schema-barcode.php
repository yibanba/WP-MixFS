<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


/**
 * 创建条形码表
 */

function create_barcode_tables($tbl_prefix) {
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
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}_barcode` (
            `bc_id` int(11) NOT NULL AUTO_INCREMENT,
            `bc_goods_name` VARCHAR(50) NOT NULL,
            `bc_barcode` VARCHAR(13) NOT NULL,
            `bc_num` int(11) NOT NULL,
            `bc_year` int(11) NOT NULL,
            `bc_type` int(11) NOT NULL,
            `bc_factory` VARCHAR(50) NULL,
            `bc_contract` VARCHAR(50) NULL,
            `bc_date` DATE NULL,
            `bc_remark` VARCHAR(50) NULL,
            PRIMARY KEY (`bc_id`),
            KEY `bc_goods_name` (`bc_goods_name`),
            KEY `bc_barcode` (`bc_barcode`),
            KEY `bc_num` (`bc_num`)
        ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$tbl_prefix}___________` (
            `x` int(11) NOT NULL
        ) $collate;"
    ];

    foreach ($table_schema_array as $table) {
        dbDelta($table);
    }
}
