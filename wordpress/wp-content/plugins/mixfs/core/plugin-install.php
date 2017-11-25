<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * 本页面为插件安装时附加操作
 * 新建角色
 * 账套管理表(accounts) ma=MixFS Account
 * 登陆日志表(user_log) User Log
 */

function do_install_core() {
    core_add_roles();
    core_install_tables();
}

function core_add_roles() {
    add_role('mixfs_superman', 'MixFS - 系统管理', array('read' => true, 'level_0' => true));
    add_role('mixfs_manager', 'MixFS - 老板', array('read' => true, 'level_0' => true));
    add_role('mixfs_operator', 'MixFS - 操作员', array('read' => true, 'level_0' => true));
}

/**
 * mixfs_accounts + mixfs_user_log
 */
function core_install_tables() {
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
        "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}mixfs_accounts` (
            `ma_id` int(11) NOT NULL AUTO_INCREMENT,
            `ma_tbl_prefix` varchar(10) NOT NULL,
            `ma_tbl_name` varchar(50) NOT NULL,
            `ma_tbl_detail` varchar(100) DEFAULT NULL,
            `ma_ID_permission` varchar(100) DEFAULT NULL,
            `ma_create_md5` varchar(32) NOT NULL,
            `ma_acc_type` varchar(100) NOT NULL,
            `ma_link_barcode` varchar(50) DEFAULT NULL,
            `ma_create_date` date NOT NULL,
            `ma_update_date` date DEFAULT NULL,
            `ma_order_by` int(11) NOT NULL DEFAULT '99',
            PRIMARY KEY (`ma_id`)
        ) $collate;",
        "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}mixfs_user_log` (
            `log_id` int(9) NOT NULL AUTO_INCREMENT,
            `uid` int(9) NOT NULL,
            `logintime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `ip` varchar(30) NOT NULL,
            `account` varchar(30) NOT NULL,
            PRIMARY KEY (`log_id`)
        ) $collate;"
    ];

    foreach ($table_schema_array as $table) {
        dbDelta($table);
    }
}
