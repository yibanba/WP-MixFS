<?php

/**
 * Plugin Name: Mix Financial Softeware
 * Plugin URI: http://mixfs.com/wordpress/plugins/
 * Description: Mix Financial Softeware is a non-accounting professional software
 * Version: 1.1
 * Author: Victor
 * Author URI: http://www.yibanba.com/
 * Author E-mail: yibanba@hotmail.com
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('MixFS')) {

    /**
     * Main MixFS Class
     */
    final class MixFS {

        public static function instance() {

            static $instance = null;

            if (null === $instance) {
                $instance = new MixFS;
                $instance->setup_globals();
                $instance->includes();
                $instance->setup_actions();
            }

            return $instance;
        }

        private function __construct() {
            /** Do nothing here */
        }

        /**
         * 设置全局变量
         */
        private function setup_globals() {

            add_action('init', array($this, 'sessionStart'), 1);
            add_action('wp_logout', array($this, 'sessionEnd'));
            add_action('wp_login', array($this, 'sessionEnd'));

            /**
             * plugin_dir     = X:\xxx/plugins/mixfs/
             * plugin_url      = http://xxx/plugins/mixfs/
             */
            $this->plugin_dir = plugin_dir_path(__FILE__);
            $this->plugin_url = plugins_url() . '/mixfs/';
        }

        /**
         * SESSION ... session_start | session_destroy
         */
        public function sessionStart() {
            if (!session_id()) {
                session_start();
            }
        }

        public function sessionEnd() {
            session_destroy();
        }

        /**
         * 包含必要文件
         */
        private function includes() {
            require_once ( $this->plugin_dir . 'init.php');
            require_once ( $this->plugin_dir . 'functions.php');
            require_once ( $this->plugin_dir . 'admin-menu.php');
        }

        /**
         * Setup the default hooks and actions
         */
        private function setup_actions() {
            register_activation_hook(__FILE__, array($this, 'activate'));
            register_deactivation_hook(__FILE__, array($this, 'deactivate'));

            add_filter('plugin_action_links', array($this, 'plugin_action_links'));
            add_action('admin_enqueue_scripts', array($this, 'js_css_img'));
        }

        function js_css_img() {
            wp_enqueue_style('jquery-ui-core', $this->plugin_url . 'js-css-img/jquery.ui.core.css', array(), '1.10.3', 'all');

            wp_register_style('jquery-ui-theme', $this->plugin_url . 'js-css-img/jquery.ui.theme.css', array(), '1.10.3', 'all');
            wp_enqueue_style('jquery-ui-theme');

            wp_enqueue_style('datepicker', $this->plugin_url . 'js-css-img/jquery.ui.datepicker.css', array(), '1.10.3', 'all');
            wp_enqueue_script('jquery-ui-datepicker');
            
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-position');
            wp_enqueue_script('jquery-ui-menu');

            wp_enqueue_style('autocomplete', $this->plugin_url . 'js-css-img/jquery.ui.autocomplete.css', array(), '1.10.3', 'all');
            wp_enqueue_script('jquery-ui-autocomplete');
        }

        /**
         * 激活插件时操作，安装基础表:
         */
        function activate() {

            $this->install(); // mixfs_accounts + mixfs_user_log

            /**
             * 把"周数"作为种子，md5(table_name + week number),
             * 区别不同账套和防止直接输入特定页面网址
             */
            update_option('mixfs_week_number', date("W", time()));
        }

        /**
         * 停用插件时操作，
         */
        function deactivate() {
            $this->uninstall();
        }

        /**
         * 激活后插件页面显示链接: 停用 | 编辑 | 软件设置 | 使用说明
         */
        public function plugin_action_links($links) {
            $plugin_links = array(
                '<a href="' . admin_url('admin.php?page=system-setup') . '">系统设置</a>',
                '<a href="http://mixfs.com/wordpress/plugins/">使用说明</a>',
            );

            return array_merge($links, $plugin_links);
        }

        /**
         * 安装核心功能：表、角色
         */
        function install() {
            require_once ( $this->plugin_dir . 'core/plugin-install.php');
            do_install_core();
        }

        /**
         * 卸载核心功能：移除Mix角色，恢复Mix用户为订阅者角色(Subscriber)
         */
        function uninstall() {
            require_once ( $this->plugin_dir . 'core/plugin-uninstall.php');
            do_uninstall_core();
        }

    }

} // Main MixFS Class

function mixfs() {

    return mixfs::instance();
}

mixfs();
