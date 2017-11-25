<?php

/**
 * 系统全部菜单
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

add_action('admin_menu', 'account_system_setup_menu');

/**
 * ########## 管理员菜单 ##########
 */
function account_system_setup_menu() {
    $cap = get_mixfs_role();    // 必须是 MixFS 管理员
    if ($cap == 'mixfs_superman') {
        add_menu_page('', 'MixFS 系统设置', $cap, 'system-setup');
        add_submenu_page('system-setup', '系统设置说明', '系统设置说明', $cap, 'system-setup', 'system_setup_guide');
        add_submenu_page('system-setup', '账套列表', '账套列表', $cap, 'system-list', 'system_list');
        add_submenu_page('system-setup', '权限分配', '权限分配', $cap, 'system-permission', 'system_permission');
        add_submenu_page('system-setup', '登录日志', '登录日志', $cap, 'system-log', 'system_log');
    }
}

function system_setup_guide() {
    require_once( 'core/system-setup.php' );
}

function system_list() {
    require_once( 'core/system-list.php' );
}

function system_permission() {
    require_once( 'core/system-permission.php' );
}

function system_log() {
    require_once( 'core/system-log.php' );
}

/**
 * ########## 所有 MixFS 用户菜单 ##########
 */
add_action('admin_menu', 'app_entrance_menu');

/**
 * MixFS 用户和管理员的公用入口
 */
function app_entrance_menu() {
    $cap = get_mixfs_role();    // 必须是 MixFS 用户或管理员
    if ($cap) {
        add_menu_page('', '财务软件入口', $cap, 'app-entrance');
        add_submenu_page('app-entrance', '财务软件入口', '财务软件入口', $cap, 'app-entrance', 'app_entrance');
        add_submenu_page('app-entrance', '财务软件说明', '财务软件说明', $cap, 'app-guide', 'app_guide');
    }
}

function app_entrance() {
    require_once( 'core/app-entrance.php' );
}

function app_guide() {
    require_once( 'core/app-guide.php' );
}

/**
 * ########## 所有 进销存 用户菜单 ##########
 */
add_action('admin_menu', 'invoicing_init');

/**
 * 进销存 初始化
 */
function invoicing_init() {

    $cap = get_mixfs_role();    // 必须是 MixFS 用户或管理员
    if ($cap && 'invoicing' == $_SESSION['acc_type']['invoicing']) {
        add_menu_page('', '添加初始信息', $cap, 'invoicing-init-guide', 'invoicing_init_guide', '', 140);
        add_submenu_page('invoicing-init-guide', '添加初始信息', '添加信息说明', $cap, 'invoicing-init-guide', 'invoicing_init_guide');
        add_submenu_page('invoicing-init-guide', '添加产成品名称', '添加产成品名称', $cap, 'invoicing-add-goods', 'invoicing_add_goods');
        add_submenu_page('invoicing-init-guide', '添加原材料名称', '添加原材料名称', $cap, 'invoicing-add-stuff', 'invoicing_add_stuff');
        add_submenu_page('invoicing-init-guide', '添加仓库店面', '添加仓库店面', $cap, 'invoicing-add-place', 'invoicing_add_place');
        add_submenu_page('invoicing-init-guide', '添加费用项目', '添加费用项目', $cap, 'invoicing-add-fee', 'invoicing_add_fee');
        add_submenu_page('invoicing-init-guide', '添加货柜号', '添加货柜号', $cap, 'invoicing-add-container', 'invoicing_add_container');
        add_submenu_page('invoicing-init-guide', '添加供应商', '添加供应商', $cap, 'invoicing-add-provider', 'invoicing_add_provider');
        add_submenu_page('invoicing-init-guide', '临时更新件双数据', '临时更新件双数据', 'superman', 'temp-update-per-pack', 'temp_update_per_pack');
    }
}

function invoicing_init_guide() {
    require_once( 'modules/invoicing-init/invoicing-init-guide.php' );
}

function invoicing_add_goods() {
    require_once( 'modules/invoicing-init/add-goods.php' );
}

function invoicing_add_stuff() {
    require_once( 'modules/invoicing-init/add-stuff.php' );
}

function invoicing_add_fee() {
    require_once( 'modules/invoicing-init/add-fee.php' );
}

function invoicing_add_place() {
    require_once( 'modules/invoicing-init/add-place.php' );
}

function invoicing_add_container() {
    require_once( 'modules/invoicing-init/add-container.php' );
}

function invoicing_add_provider() {
    require_once( 'modules/invoicing-init/add-provider.php' );
}

function temp_update_per_pack() {
    require_once( 'modules/invoicing-init/temp-update-per-pack.php' );
}

add_action('admin_menu', 'invoicing_biz');

/**
 * 进销存 业务
 */
function invoicing_biz() {
    $cap = get_mixfs_role();    // 必须是 MixFS 用户或管理员
    if ($cap && 'invoicing' == $_SESSION['acc_type']['invoicing']) {
        add_menu_page('', '业务处理', $cap, 'invoicing-biz-guide', 'invoicing_biz_guide', '', 120);
        add_submenu_page('invoicing-biz-guide', '业务处理', '业务处理', $cap, 'invoicing-biz-guide', 'invoicing_biz_guide');
        add_submenu_page('invoicing-biz-guide', '产成品订单业务', '产成品订单业务', $cap, 'goods-biz-order', 'goods_biz_order');
        add_submenu_page('invoicing-biz-guide', '费用业务', '费用业务', $cap, 'fee-biz', 'fee_biz');
        add_submenu_page('invoicing-biz-guide', '原材料业务', '原材料业务', $cap, 'stuff-biz', 'stuff_biz');
        add_submenu_page('invoicing-biz-guide', '外币兑换业务', '外币兑换业务', $cap, 'currency-biz', 'currency_biz');
    }
}

function invoicing_biz_guide() {
    require_once( 'modules/invoicing-biz/invoicing-biz-guide.php' );
}

function goods_biz_order() {
    require_once( 'modules/invoicing-biz/goods-biz-order.php' );
}

function fee_biz() {
    require_once( 'modules/invoicing-biz/fee-biz.php' );
}

function stuff_biz() {
    require_once( 'modules/invoicing-biz/stuff-biz.php' );
}

function currency_biz() {
    require_once( 'modules/invoicing-biz/currency-biz.php' );
}

add_action('admin_menu', 'invoicing_query_menu');

/**
 * 进销存 查询
 */
function invoicing_query_menu() {
    $cap = get_mixfs_role();    // 必须是 MixFS 用户或管理员
    if ($cap && 'invoicing' == $_SESSION['acc_type']['invoicing']) {
        add_menu_page('', '明细查询', $cap, 'invoicing-query-guide', 'invoicing_query_guide', '', 130);
        add_submenu_page('invoicing-query-guide', '进销存概况', '进销存概况', $cap, 'invoicing-query-guide', 'invoicing_query_guide');
        add_submenu_page('invoicing-query-guide', '现金明细汇总', '现金明细汇总', $cap, 'cash-overview', 'cash_overview');
        add_submenu_page('invoicing-query-guide', '产成品查询', '产成品查询', $cap, 'goods-qry', 'goods_qry');
        add_submenu_page('invoicing-query-guide', '费用明细查询', '费用明细查询', $cap, 'fee-qry', 'fee_qry');
        add_submenu_page('invoicing-query-guide', '原材料查询', '原材料查询', $cap, 'stuff-qry', 'stuff_qry');
        add_submenu_page('invoicing-query-guide', '产成品业务流水', '产成品业务流水', $cap, 'goods-serial-no', 'goods_serial_no');
        add_submenu_page('invoicing-query-guide', '费用业务流水', '费用业务流水', $cap, 'fee-serial-no', 'fee_serial_no');
        add_submenu_page('invoicing-query-guide', '赊销业务查询', '赊销业务查询', $cap, 'credit-qry', 'credit_qry');
    }
}

function invoicing_query_guide() {
    require_once( 'modules/invoicing-query/invoicing-query-guide.php' );
}

function cash_overview() {
    require_once( 'modules/invoicing-query/cash-overview.php' );
}

function goods_qry() {
    require_once( 'modules/invoicing-query/goods-qry.php' );
}

function fee_qry() {
    require_once( 'modules/invoicing-query/fee-qry.php' );
}

function stuff_qry() {
    require_once( 'modules/invoicing-query/stuff-qry.php' );
}

function goods_serial_no() {
    require_once( 'modules/invoicing-query/goods-serial-no.php' );
}

function fee_serial_no() {
    require_once( 'modules/invoicing-query/fee-serial-no.php' );
}

function credit_qry() {
    require_once( 'modules/invoicing-query/credit-qry.php' );
}

/**
 * ########## 往来账专项菜单 ##########
 */
add_action('admin_menu', 'current_acc_menu');

function current_acc_menu() {
    $cap = get_mixfs_role();    // 必须是 MixFS 用户或管理员
    if ($cap && 'current_acc' == $_SESSION['acc_type']['current_acc']) {
        add_menu_page('', '往来账款业务', $cap, 'current-acc-guide');
        add_submenu_page('current-acc-guide', '往来账款概况', '往来账款概况', $cap, 'current-acc-guide', 'current_acc_guide');
        add_submenu_page('current-acc-guide', '往来账款查询', '往来账款查询', $cap, 'current-acc-query', 'current_acc_query');
        add_submenu_page('current-acc-guide', '往来账款业务', '往来账款业务', $cap, 'current-acc-biz', 'current_acc_biz');
    }
}

function current_acc_guide() {
    require_once( 'modules/current-acc/current-acc-guide.php' );
}

function current_acc_query() {
    require_once( 'modules/current-acc/current-acc-query.php' );
}

function current_acc_biz() {
    require_once( 'modules/current-acc/current-acc-biz.php' );
}

/**
 * ########## 条形码专项菜单 ##########
 */
add_action('admin_menu', 'barcode_menu');

function barcode_menu() {
    $cap = get_mixfs_role();    // 必须是 MixFS 用户或管理员
    if ($cap && 'barcode' == $_SESSION['acc_type']['barcode']) {
        add_menu_page('', '条形码相关业务', $cap, 'barcode-guide');
        add_submenu_page('barcode-guide', '条形码业务说明', '条形码业务说明', $cap, 'barcode-guide', 'barcode_guide');
        add_submenu_page('barcode-guide', '条形码查询', '条形码查询', $cap, 'barcode-query', 'barcode_query');
        add_submenu_page('barcode-guide', '条形码录入', '条形码录入', $cap, 'barcode-import', 'barcode_import');
    }
}

function barcode_guide() {
    require_once( 'modules/barcode-biz/barcode-guide.php' );
}

function barcode_query() {
    require_once( 'modules/barcode-biz/barcode-query.php' );
}

function barcode_import() {
    require_once( 'modules/barcode-biz/barcode-import.php' );
}
