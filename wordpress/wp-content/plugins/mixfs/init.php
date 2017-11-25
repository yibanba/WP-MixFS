<?php

if (!defined('ABSPATH'))
    exit;
/**
 * 用户登录、退出时执行的操作
 * 获取用户登录后相关信息
 * 利用 hooks + filters 定制上下工具条和菜单
 */
add_action('template_redirect', 'login_jump');

/**
 * 访问本网直接跳转登录页面
 */
function login_jump() {
    if (!is_user_logged_in()) {
        wp_safe_redirect(wp_login_url());
    }
}

add_filter('login_redirect', 'mixfs_login_redirect', 10, 3);

/**
 * 不同角色登陆后跳转相应页面
 */
function mixfs_login_redirect($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('administrator', $user->roles) || in_array('subscriber', $user->roles)) {
            return admin_url();
        } else if (in_array('mixfs_superman', $user->roles)) {
            return admin_url('admin.php?page=system-setup');
        } else {
            return admin_url('admin.php?page=app-entrance');
        }
    } else {
        return $redirect_to;
    }
}

add_action('init', 'login_user_get_acc_type');

/**
 * 获得登录用户可操作的账户类型
 */
function login_user_get_acc_type() {
    global $wpdb, $current_user;
    $_SESSION['acc_type'] = array();  // 存储当前用户可以操作哪些类型的账套
    $results_accounts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mixfs_accounts", ARRAY_A);
    foreach ($results_accounts as $tbl) {
        if (in_array($current_user->ID, explode(",", $tbl['ma_ID_permission']))) {
            $_SESSION['acc_type'][$tbl['ma_acc_type']] = $tbl['ma_acc_type'];
        }
    }
}

add_action('wp_logout', 'unset_sessions');

/**
 * 退出登录后销毁 $_SESSION
 */
function unset_sessions() {
    unset($_SESSION['acc_type']);
    unset($_SESSION['mas']);
    wp_safe_redirect(wp_login_url());
}

/**
 * ########## 定制后台菜单 + 上下工具栏 ##########
 */
/**
 * 去除升级提示
 */
add_action('admin_menu', 'wp_hide_nag');

function wp_hide_nag() {
    global $current_user;
    if ($current_user->roles[0] != 'administrator') {
        remove_action('admin_notices', 'update_nag', 3);
    }
}

/**
 * $wp_admin_bar->remove_menu('updates');      //移除升级通知
 * $wp_admin_bar->remove_menu('comments');     //移除评论
 * $wp_admin_bar->remove_menu('new-content');  // 移除“新建”
 * $wp_admin_bar->remove_menu('my-sites');   //移除我的网站(多站点)
 * $wp_admin_bar->remove_menu('search');     //移除搜索
 * $wp_admin_bar->remove_menu('my-account'); //移除个人中心
 * $wp_admin_bar->add_menu();  // 添加自定义菜单
 */
add_action('wp_before_admin_bar_render', 'modify_admin_bar');

function modify_admin_bar() {
    global $wp_admin_bar, $current_user;

    if ($current_user->roles[0] != 'administrator') {

        $wp_admin_bar->remove_menu('wp-logo');      //移除Logo
        $wp_admin_bar->remove_menu('site-name');    //移除网站名称
        $wp_admin_bar->remove_node('dashboard');    //移除网站名称

        $wp_admin_bar->add_menu(array(
            'id' => 'validity',
            'title' => '服务器及数据库租用有效期',
            'href' => ''
        ));
        $wp_admin_bar->add_menu(array(
            'id' => 'date',
            'title' => ' 2016-11-30 —— 2018-11-30  ',
            'href' => '',
            'parent' => 'validity'
        ));
    }
}

/**
 * remove_menu_page( 'jetpack' );                    //Jetpack* 
 * remove_menu_page( 'edit.php' );                   //Posts
 * remove_menu_page( 'upload.php' );                 //Media
 * remove_menu_page( 'edit.php?post_type=page' );    //Pages
 * remove_menu_page( 'edit-comments.php' );          //Comments
 * remove_menu_page( 'themes.php' );                 //Appearance
 * remove_menu_page( 'plugins.php' );                //Plugins
 * remove_menu_page( 'users.php' );                  //Users
 * remove_menu_page( 'tools.php' );                  //Tools
 * remove_menu_page( 'options-general.php' );        //Settings
 */
add_action('admin_init', 'remove_menu');

function remove_menu() {
    global $current_user;
    if ($current_user->roles[0] != 'administrator') {
        remove_menu_page('index.php');                  //Dashboard
    }
}

/**
 * 替换底部工具栏左侧信息
 */
add_filter('admin_footer_text', 'admin_footer_left_text');

function admin_footer_left_text($text) {
    $text = '<span id="footer-thankyou">米克斯财会软件 <a href="http://www.mixfs.com/">www.mixfs.com</a></span>';
    return $text;
}

/**
 * 替换底部工具栏右侧信息
 */
add_filter('update_footer', 'admin_footer_right_text', 11);

function admin_footer_right_text($text) { // 右边信息
    $text = "QQ: 55517131 &nbsp; E-mail: sph999@hotmail.com";
    return $text;
}
