<?php

/**
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */
defined('ABSPATH') || exit;

use Duplicator\Lite as Duplicator;

/* @var $currentPluginBootFile string */

// CHECK IF PLUGIN CAN BE EXECTUED
require_once(__DIR__ . "/tools/Lite/Requirements.php");
if (Duplicator\Requirements::canRun($currentPluginBootFile) === false) {
    return;
} else {
    // NOTE: Plugin code must be inside a conditional block to prevent functions definition, simple return is not enough
    define('DUPLICATOR_LITE_PATH', dirname($currentPluginBootFile));
    define('DUPLICATOR_LITE_FILE', $currentPluginBootFile);
    define('DUPLICATOR_LITE_PLUGIN_URL', plugins_url('', $currentPluginBootFile));
    
    if (!defined('DUPXABSPATH')) {
        define('DUPXABSPATH', dirname(DUPLICATOR_LITE_FILE));
    }

    require_once("helper.php");
    require_once("define.php");
    if (is_admin() == true) {
        if (defined('DUPLICATOR_DEACTIVATION_FEEDBACK') && DUPLICATOR_DEACTIVATION_FEEDBACK) {
            require_once 'deactivation.php';
        }
        require_once 'lib/snaplib/snaplib.all.php';
        require_once 'classes/class.constants.php';
        require_once 'classes/host/class.custom.host.manager.php';
        require_once 'classes/class.settings.php';
        require_once 'classes/class.logging.php';
        require_once 'classes/class.plugin.upgrade.php';
        require_once 'classes/utilities/class.u.php';
        require_once 'classes/utilities/class.u.string.php';
        require_once 'classes/utilities/class.u.validator.php';
        require_once 'classes/class.db.php';
        require_once 'classes/class.server.php';
        require_once 'classes/ui/class.ui.viewstate.php';
        require_once 'classes/ui/class.ui.notice.php';
        require_once 'classes/package/class.pack.php';
        require_once 'views/packages/screen.php';

        //Controllers
        require_once 'ctrls/ctrl.package.php';
        require_once 'ctrls/ctrl.tools.php';
        require_once 'ctrls/ctrl.ui.php';
        require_once 'ctrls/class.web.services.php';

        //Init Class
        DUP_Custom_Host_Manager::getInstance()->init();
        DUP_Settings::init();
        DUP_Log::Init();
        DUP_Util::init();
        DUP_DB::init();

        /** ========================================================
         * ACTIVATE/DEACTIVE/UPDATE HOOKS
         * =====================================================  */
        register_activation_hook(DUPLICATOR_LITE_FILE, array('DUP_LITE_Plugin_Upgrade', 'onActivationAction'));
        register_deactivation_hook(DUPLICATOR_LITE_FILE, 'duplicator_deactivate');

        /**
         * Hooked into `plugins_loaded`.  Routines used to update the plugin
         *
         * @access global
         * @return null
         */
        function duplicator_update()
        {
            if (DUPLICATOR_VERSION != get_option(DUP_LITE_Plugin_Upgrade::DUP_VERSION_OPT_KEY)) {
                DUP_LITE_Plugin_Upgrade::onActivationAction();
                // $snapShotDirPerm = substr(sprintf("%o", fileperms(DUP_Settings::getSsdirPath())),-4);
            }
            load_plugin_textdomain('duplicator');
        }

        /**
         * Hooked into `register_deactivation_hook`.  Routines used to deactivate the plugin
         * For uninstall see uninstall.php  WordPress by default will call the uninstall.php file
         *
         * @access global
         * @return null
         */
        function duplicator_deactivate()
        {
            //Logic has been added to uninstall.php
        }
        /** ========================================================
         * ACTION HOOKS
         * =====================================================  */
        add_action('plugins_loaded', 'duplicator_update');
        add_action('plugins_loaded', 'duplicator_wpfront_integrate');

        function duplicator_load_textdomain()
        {
            load_plugin_textdomain('duplicator', false, false);
        }
        add_action('plugins_loaded', 'duplicator_load_textdomain');

        add_action('admin_init', 'duplicator_admin_init');
        add_action('admin_menu', 'duplicator_menu');
        add_action('admin_enqueue_scripts', 'duplicator_admin_enqueue_scripts');
        DUP_UI_Notice::init();

        //CTRL ACTIONS
        DUP_Web_Services::init();
        add_action('wp_ajax_duplicator_active_package_info', 'duplicator_active_package_info');
        add_action('wp_ajax_duplicator_package_scan', 'duplicator_package_scan');
        add_action('wp_ajax_duplicator_package_build', 'duplicator_package_build');
        add_action('wp_ajax_duplicator_package_delete', 'duplicator_package_delete');
        add_action('wp_ajax_duplicator_duparchive_package_build', 'duplicator_duparchive_package_build');

        $GLOBALS['CTRLS_DUP_CTRL_UI']      = new DUP_CTRL_UI();
        $GLOBALS['CTRLS_DUP_CTRL_Tools']   = new DUP_CTRL_Tools();
        $GLOBALS['CTRLS_DUP_CTRL_Package'] = new DUP_CTRL_Package();

        /**
         * User role editor integration 
         *
         * @access global
         * @return null
         */
        function duplicator_wpfront_integrate()
        {
            if (DUP_Settings::Get('wpfront_integrate')) {
                do_action('wpfront_user_role_editor_duplicator_init', array('export', 'manage_options', 'read'));
            }
        }

        /**
         * Hooked into `admin_init`.  Init routines for all admin pages 
         *
         * @access global
         * @return null
         */
        function duplicator_admin_init()
        {
            /* CSS */
            wp_register_style('dup-jquery-ui', DUPLICATOR_PLUGIN_URL . 'assets/css/jquery-ui.css', null, "1.11.2");
            wp_register_style('dup-font-awesome', DUPLICATOR_PLUGIN_URL . 'assets/css/fontawesome-all.min.css', null, '5.7.2');
            wp_register_style('dup-plugin-global-style', DUPLICATOR_PLUGIN_URL . 'assets/css/global_admin_style.css', null, DUPLICATOR_VERSION);
            wp_register_style('dup-plugin-style', DUPLICATOR_PLUGIN_URL . 'assets/css/style.css', array('dup-plugin-global-style'), DUPLICATOR_VERSION);

            wp_register_style('dup-jquery-qtip', DUPLICATOR_PLUGIN_URL . 'assets/js/jquery.qtip/jquery.qtip.min.css', null, '2.2.1');
            wp_register_style('dup-parsley-style', DUPLICATOR_PLUGIN_URL . 'assets/css/parsley.css', null, '2.3.5');
            /* JS */
            wp_register_script('dup-handlebars', DUPLICATOR_PLUGIN_URL . 'assets/js/handlebars.min.js', array('jquery'), '4.0.10');
            wp_register_script('dup-parsley', DUPLICATOR_PLUGIN_URL . 'assets/js/parsley.min.js', array('jquery'), '1.1.18');
            wp_register_script('dup-jquery-qtip', DUPLICATOR_PLUGIN_URL . 'assets/js/jquery.qtip/jquery.qtip.min.js', array('jquery'), '2.2.1');

            add_action('admin_head', array('DUP_UI_Screen', 'getCustomCss'));
            // Clean tmp folder
            DUP_Package::not_active_files_tmp_cleanup();

            $unhook_third_party_js  = DUP_Settings::Get('unhook_third_party_js');
            $unhook_third_party_css = DUP_Settings::Get('unhook_third_party_css');
            if ($unhook_third_party_js || $unhook_third_party_css) {
                add_action('admin_enqueue_scripts', 'duplicator_unhook_third_party_assets', 99999, 1);
            }
        }

        /**
         * Hooked into `admin_enqueue_scripts`.  Init routines for all admin pages
         *
         * @access global
         * @return null
         */
        function duplicator_admin_enqueue_scripts()
        {
            wp_enqueue_script('dup-global-script', DUPLICATOR_PLUGIN_URL . 'assets/js/global-admin-script.js', array('jquery'), DUPLICATOR_VERSION, true);
            wp_localize_script('dup-global-script',
                               'dup_global_script_data',
                               array(
                    'duplicator_admin_notice_to_dismiss' => wp_create_nonce('duplicator_admin_notice_to_dismiss')
                )
            );
            wp_enqueue_style('dup-plugin-global-style');
        }

        /**
         * Redirects the clicked menu item to the correct location
         *
         * @access global
         * @return null
         */
        function duplicator_get_menu()
        {
            $current_page = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : 'duplicator';
            switch ($current_page) {
                case 'duplicator': include('views/packages/controller.php'); break;
                case 'duplicator-settings': include('views/settings/controller.php'); break;
                case 'duplicator-tools': include('views/tools/controller.php'); break;
                case 'duplicator-debug': include('debug/main.php'); break;
                case 'duplicator-gopro': include('views/settings/gopro.php'); break;
            }
        }

        /**
         * Hooked into `admin_menu`.  Loads all of the wp left nav admin menus for Duplicator
         *
         * @access global
         * @return null
         */
        function duplicator_menu()
        {
            $wpfront_caps_translator = 'wpfront_user_role_editor_duplicator_translate_capability';
            //SVG Icon: See https://websemantics.uk/tools/image-to-data-uri-converter/
            //older version
            //$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQXJ0d29yayIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIyMy4yNXB4IiBoZWlnaHQ9IjIyLjM3NXB4IiB2aWV3Qm94PSIwIDAgMjMuMjUgMjIuMzc1IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAyMy4yNSAyMi4zNzUiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxwYXRoIGZpbGw9IiM5Q0ExQTYiIGQ9Ik0xOC4wMTEsMS4xODhjLTEuOTk1LDAtMy42MTUsMS42MTgtMy42MTUsMy42MTRjMCwwLjA4NSwwLjAwOCwwLjE2NywwLjAxNiwwLjI1TDcuNzMzLDguMTg0QzcuMDg0LDcuNTY1LDYuMjA4LDcuMTgyLDUuMjQsNy4xODJjLTEuOTk2LDAtMy42MTUsMS42MTktMy42MTUsMy42MTRjMCwxLjk5NiwxLjYxOSwzLjYxMywzLjYxNSwzLjYxM2MwLjYyOSwwLDEuMjIyLTAuMTYyLDEuNzM3LTAuNDQ1bDIuODksMi40MzhjLTAuMTI2LDAuMzY4LTAuMTk4LDAuNzYzLTAuMTk4LDEuMTczYzAsMS45OTUsMS42MTgsMy42MTMsMy42MTQsMy42MTNjMS45OTUsMCwzLjYxNS0xLjYxOCwzLjYxNS0zLjYxM2MwLTEuOTk3LTEuNjItMy42MTQtMy42MTUtMy42MTRjLTAuNjMsMC0xLjIyMiwwLjE2Mi0xLjczNywwLjQ0M2wtMi44OS0yLjQzNWMwLjEyNi0wLjM2OCwwLjE5OC0wLjc2MywwLjE5OC0xLjE3M2MwLTAuMDg0LTAuMDA4LTAuMTY2LTAuMDEzLTAuMjVsNi42NzYtMy4xMzNjMC42NDgsMC42MTksMS41MjUsMS4wMDIsMi40OTUsMS4wMDJjMS45OTQsMCwzLjYxMy0xLjYxNywzLjYxMy0zLjYxM0MyMS42MjUsMi44MDYsMjAuMDA2LDEuMTg4LDE4LjAxMSwxLjE4OHoiLz48L3N2Zz4=';
            $icon_svg                = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxNS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB3aWR0aD0iMjU2cHgiIGhlaWdodD0iMjU2cHgiIHZpZXdCb3g9IjAgMCAyNTYgMjU2IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAyNTYgMjU2IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KCTxnPg0KCQk8cGF0aCBmaWxsPSIjQTdBOUFDIiBkPSJNMTcyLjEwMywzNS4yMjNsLTEuMzk1LTI0LjA5N0wxNTMuMzYsNi40NzhsLTEzLjI1MywyMC4xNzFjLTYuNzQyLTAuNjc1LTEzLjUzNS0wLjY5Ni0yMC4yNzYtMC4wNDINCgkJCUwxMDYuNDY3LDYuMjdsLTE3LjM0OCw0LjY0N2wtMS40LDI0LjIwNGMtNi4wNzMsMi43MjQtMTEuOTMsNi4wNzQtMTcuNDg1LDEwLjAzOUw0OC40MDMsMzQuMTgzTDM1LjcwNCw0Ni44ODJsMTAuOTEsMjEuNzAxDQoJCQljLTQuMDExLDUuNTIyLTcuMzk2LDExLjM1Mi0xMC4xNywxNy4zOTdsLTI0LjM2NywxLjQxbC00LjY0OCwxNy4zNDhsMjAuMjQxLDEzLjNjLTAuNzA4LDYuNzM0LTAuNzg1LDEzLjUyMy0wLjE2NiwyMC4yNjUNCgkJCWwtMjAuMjg0LDEzLjMzbDQuNjQ4LDE3LjM0OGwyMy4zMjQsMS4zNDlsMC4yMjctMC44MjZsOS4xMDYtMzMuMTc2Yy0yLjEyNS0yNC4zMzMsNi4xMDQtNDkuMzk3LDI0LjcyOS02OC4wMjMNCgkJCWMyNy4zOTMtMjcuMzkzLDY4LjcxLTMyLjMxNSwxMDEuMTQ1LTE0LjgzM0w1NC40MjIsMTY5LjQ0N2wtMi41ODUtMzIuMzU1TDMwLjg5LDIxMy4zOThsMzEuNjE0LTMxLjYxNEwxODIuNzM1LDYxLjU1Mw0KCQkJbDQuMjA0LTQuMjA2bDcuOTc4LTcuOTc4QzE4Ny44MzYsNDMuNTU3LDE4MC4xNSwzOC44NTcsMTcyLjEwMywzNS4yMjN6Ii8+DQoJCTxwYXRoIGZpbGw9IiNBN0E5QUMiIGQ9Ik0xMDUuMjE0LDkuNTU4bDEyLjIzMSwxOC42MTRsMC45NDUsMS40NGwxLjcxNS0wLjE2NmMzLjE4Mi0wLjMwOCw2LjQyMy0wLjQ2NSw5LjYzNC0wLjQ2NQ0KCQkJYzMuMzQ3LDAsNi43MzYsMC4xNywxMC4wODIsMC41MDZsMS43MTksMC4xNzJsMC45NS0xLjQ0NGwxMi4xMjItMTguNDQ5bDEzLjM2NSwzLjU4MWwxLjI3NCwyMi4wNDFsMC4xMDEsMS43MjRsMS41NzMsMC43MTENCgkJCWM3LjAzNiwzLjE3NSwxMy42NTIsNy4xMzYsMTkuNzExLDExLjc5MWwtNS43MTcsNS43MThsLTQuMjAzLDQuMjAzTDYwLjQ4NSwxNzkuNzY2bC0yMy45OTEsMjMuOTkybDEzLjc5Mi01MC4yNDRsMS4yOTIsMTYuMTYyDQoJCQlsMC40OTMsNi4xNTlsNC4zNjktNC4zNjdMMTcyLjQxNiw1NS40OWwyLjcwOS0yLjcxMWwtMy4zNzItMS44MThjLTEyLjgyOS02LjkxNS0yNy4zNDktMTAuNTcxLTQxLjk5NC0xMC41NzENCgkJCWMtMjMuNjIsMC00NS44MjMsOS4xOTgtNjIuNTIyLDI1Ljg5N0M0OC44MzMsODQuNjksMzkuNTIsMTEwLjA5NSw0MS42MzksMTM2LjA2MWwtOC41ODcsMzEuMjg4bC0xOC45NjItMS4wOTlsLTMuNTgxLTEzLjM2Mw0KCQkJbDE4LjU2Mi0xMi4xOThsMS40MzEtMC45NDJsLTAuMTU2LTEuNzA0Yy0wLjU5Mi02LjQzNi0wLjUzOC0xMy4wNjUsMC4xNjEtMTkuNzA2bDAuMTgyLTEuNzI4bC0xLjQ1Mi0wLjk1NWwtMTguNTE4LTEyLjE2Nw0KCQkJbDMuNTgxLTEzLjM2NmwyMi4zMDktMS4yOTFsMS43MTItMC4wOThsMC43MTctMS41NTljMi43MjktNS45NDgsNi4wNTUtMTEuNjM5LDkuODg1LTE2LjkxM2wxLjAyMS0xLjQwNmwtMC43NzktMS41NTINCgkJCWwtOS45ODQtMTkuODU5bDkuNzg0LTkuNzg0bDE5Ljk4OCwxMC4wNDlsMS41MzgsMC43NzRsMS40MDEtMS4wMDFjNS4zNDMtMy44MTEsMTEuMDYxLTcuMDk0LDE2Ljk5Ny05Ljc1N2wxLjU4MS0wLjcxMmwwLjEtMS43MjgNCgkJCWwxLjI4MS0yMi4xNDVMMTA1LjIxNCw5LjU1OCBNMTA2LjQ2Nyw2LjI3bC0xNy4zNDgsNC42NDdsLTEuNCwyNC4yMDRjLTYuMDczLDIuNzI2LTExLjkzLDYuMDc0LTE3LjQ4NiwxMC4wMzlsLTIxLjgzLTEwLjk3Ng0KCQkJbC0xMi43LDEyLjcwMWwxMC45MSwyMS42OTljLTQuMDExLDUuNTIyLTcuMzk2LDExLjM1My0xMC4xNywxNy4zOTdsLTI0LjM2NywxLjQxbC00LjY0OCwxNy4zNDhsMjAuMjQsMTMuMw0KCQkJYy0wLjcwOCw2LjczNC0wLjc4NCwxMy41MjMtMC4xNjUsMjAuMjY1bC0yMC4yODQsMTMuMzNsNC42NDgsMTcuMzQ4bDIzLjMyNCwxLjM0OWwwLjIyNy0wLjgyNmw5LjEwNi0zMy4xNzYNCgkJCWMtMi4xMjUtMjQuMzMzLDYuMTA0LTQ5LjM5NywyNC43MjktNjguMDIzYzE2LjcxLTE2LjcxMSwzOC42MDctMjUuMDYsNjAuNTA1LTI1LjA2YzEzLjk5OCwwLDI3Ljk5MiwzLjQxMSw0MC42NCwxMC4yMjcNCgkJCUw1NC40MjIsMTY5LjQ0OWwtMi41ODUtMzIuMzU3TDMwLjg5LDIxMy4zOThsMzEuNjE0LTMxLjYxNEwxODIuNzM1LDYxLjU1M2w0LjIwMy00LjIwNGw3Ljk3OS03Ljk3OQ0KCQkJYy03LjA4My01LjgxNS0xNC43NjctMTAuNTEzLTIyLjgxNC0xNC4xNDdsLTEuMzk1LTI0LjA5N0wxNTMuMzYsNi40NzhsLTEzLjI1NCwyMC4xN2MtMy40NDctMC4zNDYtNi45MDctMC41Mi0xMC4zNjYtMC41Mg0KCQkJYy0zLjMwNywwLTYuNjE0LDAuMTYtOS45MSwwLjQ3OUwxMDYuNDY3LDYuMjdMMTA2LjQ2Nyw2LjI3eiIvPg0KCTwvZz4NCgk8Zz4NCgkJPHBhdGggZmlsbD0iI0E3QTlBQyIgZD0iTTg3LjgwMiwyMjIuMjFsMS4zOTQsMjQuMDk3bDE3LjM0OCw0LjY0OWwxMy4yNTUtMjAuMTdjNi43NDIsMC42NzUsMTMuNTMzLDAuNjkzLDIwLjI3NCwwLjA0MQ0KCQkJbDEzLjM2NSwyMC4zMzVsMTcuMzQ3LTQuNjQ2bDEuMzk5LTI0LjIwMmM2LjA3My0yLjcyNSwxMS45My02LjA3NCwxNy40ODYtMTAuMDM4bDIxLjgzMSwxMC45NzRsMTIuNjk5LTEyLjY5OGwtMTAuOTEtMjEuNzAxDQoJCQljNC4wMTItNS41MjEsNy4zOTYtMTEuMzUyLDEwLjE2OS0xNy4zOThsMjQuMzY5LTEuNDA4bDQuNjQ2LTE3LjM0OGwtMjAuMjM5LTEzLjNjMC43MDgtNi43MzYsMC43ODQtMTMuNTIzLDAuMTY0LTIwLjI2Ng0KCQkJbDIwLjI4NC0xMy4zMjhsLTQuNjQ3LTE3LjM0OGwtMjMuMzIzLTEuMzQ5bC0wLjIyOCwwLjgyNWwtOS4xMDcsMzMuMTc1YzIuMTI3LDI0LjMzMi02LjEwNCw0OS4zOTctMjQuNzI5LDY4LjAyNA0KCQkJYy0yNy4zOTIsMjcuMzkzLTY4LjcwOSwzMi4zMTUtMTAxLjE0NCwxNC44MzFMMjA1LjQ4LDg3Ljk4NGwyLjU4NiwzMi4zNTZsMjAuOTQ4LTc2LjMwNWwtMzEuNjE1LDMxLjYxM0w3Ny4xNjksMTk1Ljg4DQoJCQlsLTQuMjA2LDQuMjA1bC03Ljk3OCw3Ljk3OUM3Mi4wNjgsMjEzLjg3Niw3OS43NTIsMjE4LjU3NSw4Ny44MDIsMjIyLjIxeiIvPg0KCQk8cGF0aCBmaWxsPSIjQTdBOUFDIiBkPSJNMjIzLjQwOSw1My42NzZsLTEzLjc5Myw1MC4yNGwtMS4yOS0xNi4xNmwtMC40OTQtNi4xNTlsLTQuMzY4LDQuMzdMODcuNDg3LDIwMS45NDJsLTIuNzA5LDIuNzEyDQoJCQlsMy4zNzMsMS44MThjMTIuODI4LDYuOTE0LDI3LjM1MSwxMC41NjgsNDEuOTk3LDEwLjU2OGMyMy42MTgsMCw0NS44MjEtOS4xOTUsNjIuNTItMjUuODk2DQoJCQljMTguNDAzLTE4LjQwMiwyNy43MTctNDMuODA3LDI1LjU5OC02OS43NzVsOC41ODgtMzEuMjgzbDE4Ljk2MSwxLjA5N2wzLjU4MiwxMy4zNjRsLTE4LjU2MywxMi4xOTdsLTEuNDMsMC45NDFsMC4xNTUsMS43MDUNCgkJCWMwLjU5Miw2LjQzNiwwLjUzOSwxMy4wNjctMC4xNiwxOS43MDZsLTAuMTgzLDEuNzI3bDEuNDUxLDAuOTU0bDE4LjUyMSwxMi4xNzFsLTMuNTgyLDEzLjM2NWwtMjIuMzExLDEuMjkxbC0xLjcxMiwwLjA5OQ0KCQkJbC0wLjcxNiwxLjU2Yy0yLjcyNyw1Ljk0NC02LjA1MywxMS42MzMtOS44ODYsMTYuOTExbC0xLjAyLDEuNDA0bDAuNzgsMS41NTRsOS45ODMsMTkuODU5bC05Ljc4NSw5Ljc4M2wtMTkuOTktMTAuMDUNCgkJCWwtMS41MzYtMC43NzJsLTEuNDAyLDAuOTk5Yy01LjM0MSwzLjgxNC0xMS4wNTksNy4wOTYtMTYuOTk0LDkuNzU4bC0xLjU4MiwwLjcxbC0wLjA5OSwxLjcyOWwtMS4yODMsMjIuMTQ2bC0xMy4zNjMsMy41ODENCgkJCWwtMTIuMjMzLTE4LjYxNWwtMC45NDYtMS40MzhsLTEuNzEzLDAuMTYzYy0zLjE4LDAuMzEtNi40MTcsMC40NjUtOS42MjYsMC40NjVjLTMuMzQ4LDAtNi43NDMtMC4xNjktMTAuMDktMC41MDVsLTEuNzE5LTAuMTcxDQoJCQlsLTAuOTUsMS40NDNsLTEyLjEyMiwxOC40NDhsLTEzLjM2Ni0zLjU4MWwtMS4yNzUtMjIuMDM4bC0wLjEtMS43MjdsLTEuNTc0LTAuNzA5Yy03LjAzNS0zLjE4LTEzLjY1My03LjEzOS0xOS43MS0xMS43OTINCgkJCWw1LjcxNi01LjcxNWw0LjIwNS00LjIwN0wxOTkuNDE4LDc3LjY2NkwyMjMuNDA5LDUzLjY3NiBNMjI5LjAxNSw0NC4wMzZsLTMxLjYxNSwzMS42MTNMNzcuMTY5LDE5NS44OGwtNC4yMDYsNC4yMDVsLTcuOTc3LDcuOTc5DQoJCQljNy4wOCw1LjgxMiwxNC43NjUsMTAuNTExLDIyLjgxNCwxNC4xNDZsMS4zOTQsMjQuMDk3bDE3LjM0OCw0LjY0OWwxMy4yNTQtMjAuMTczYzMuNDQ4LDAuMzQ4LDYuOTEyLDAuNTIzLDEwLjM3NCwwLjUyMw0KCQkJYzMuMzA0LDAsNi42MDctMC4xNjIsOS45LTAuNDc5bDEzLjM2NSwyMC4zMzVsMTcuMzQ3LTQuNjQ2bDEuMzk5LTI0LjIwMmM2LjA3My0yLjcyNSwxMS45MzEtNi4wNzcsMTcuNDg2LTEwLjAzOGwyMS44MzEsMTAuOTc0DQoJCQlsMTIuNjk5LTEyLjY5OGwtMTAuOTEtMjEuNzAxYzQuMDEyLTUuNTIxLDcuMzk2LTExLjM1MiwxMC4xNjktMTcuMzk4bDI0LjM2OS0xLjQwOGw0LjY0OS0xNy4zNDhsLTIwLjI0Mi0xMy4zDQoJCQljMC43MDgtNi43MzYsMC43ODQtMTMuNTIzLDAuMTY0LTIwLjI2NmwyMC4yODUtMTMuMzI4bC00LjY0OC0xNy4zNDhsLTIzLjMyNC0xLjM0OWwtMC4yMjcsMC44MjVsLTkuMTA3LDMzLjE3NQ0KCQkJYzIuMTI3LDI0LjMzMi02LjEwNCw0OS40MDEtMjQuNzI5LDY4LjAyNGMtMTYuNzA5LDE2LjcxLTM4LjYwNCwyNS4wNjEtNjAuNTAxLDI1LjA2MWMtMTMuOTk4LDAtMjcuOTk1LTMuNDEtNDAuNjQzLTEwLjIyOQ0KCQkJTDIwNS40OCw4Ny45ODRsMi41ODYsMzIuMzU2TDIyOS4wMTUsNDQuMDM2TDIyOS4wMTUsNDQuMDM2eiIvPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K';

            //Main Menu
            $perms                         = 'export';
            $perms                         = apply_filters($wpfront_caps_translator, $perms);
            $main_menu                     = add_menu_page('Duplicator Plugin', 'Duplicator', $perms, 'duplicator', 'duplicator_get_menu', $icon_svg);
            $perms                         = 'export';
            $perms                         = apply_filters($wpfront_caps_translator, $perms);
            $lang_txt                      = esc_html__('Packages', 'duplicator');
            $page_packages                 = add_submenu_page('duplicator', $lang_txt, $lang_txt, $perms, 'duplicator', 'duplicator_get_menu');
            $GLOBALS['DUP_Package_Screen'] = new DUP_Package_Screen($page_packages);

            $perms      = 'manage_options';
            $perms      = apply_filters($wpfront_caps_translator, $perms);
            $lang_txt   = esc_html__('Tools', 'duplicator');
            $page_tools = add_submenu_page('duplicator', $lang_txt, $lang_txt, $perms, 'duplicator-tools', 'duplicator_get_menu');

            $perms         = 'manage_options';
            $perms         = apply_filters($wpfront_caps_translator, $perms);
            $lang_txt      = esc_html__('Settings', 'duplicator');
            $page_settings = add_submenu_page('duplicator', $lang_txt, $lang_txt, $perms, 'duplicator-settings', 'duplicator_get_menu');

            $perms                   = 'manage_options';
            $admin_color             = get_user_option('admin_color');
            $orange_for_admin_colors = array(
                'fresh',
                'coffee',
                'ectoplasm',
                'midnight'
            );
            $style                   = in_array($admin_color, $orange_for_admin_colors) ? 'style="color:#f18500"' : '';
            $lang_txt                = esc_html__('Go Pro!', 'duplicator');
            $go_pro_link             = '<span ' . $style . '>' . $lang_txt . '</span>';
            $perms                   = apply_filters($wpfront_caps_translator, $perms);
            $page_gopro              = add_submenu_page('duplicator', $go_pro_link, $go_pro_link, $perms, 'duplicator-gopro', 'duplicator_get_menu');

            //Apply Scripts
            add_action('admin_print_scripts-' . $page_packages, 'duplicator_scripts');
            add_action('admin_print_scripts-' . $page_settings, 'duplicator_scripts');
            add_action('admin_print_scripts-' . $page_tools, 'duplicator_scripts');
            add_action('admin_print_scripts-' . $page_gopro, 'duplicator_scripts');

            add_action('wp_enqueue_scripts', 'duplicator_global_scripts');

            //Apply Styles
            add_action('admin_print_styles-' . $page_packages, 'duplicator_styles');
            add_action('admin_print_styles-' . $page_settings, 'duplicator_styles');
            add_action('admin_print_styles-' . $page_tools, 'duplicator_styles');
            add_action('admin_print_styles-' . $page_gopro, 'duplicator_styles');
        }

        /**
         * Loads all required javascript libs/source for DupPro
         *
         * @access global
         * @return null
         */
        function duplicator_scripts()
        {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-progressbar');
            wp_enqueue_script('dup-parsley');
            wp_enqueue_script('dup-jquery-qtip');
        }

        /**
         * Loads all CSS style libs/source for DupPro
         *
         * @access global
         * @return null
         */
        function duplicator_styles()
        {
            wp_enqueue_style('dup-jquery-ui');
            wp_enqueue_style('dup-font-awesome');
            wp_enqueue_style('dup-plugin-style');
            wp_enqueue_style('dup-jquery-qtip');
        }
        /** ========================================================
         * FILTERS
         * =====================================================  */
        add_filter('plugin_action_links', 'duplicator_manage_link', 10, 2);
        add_filter('plugin_row_meta', 'duplicator_meta_links', 10, 2);

        /**
         * Adds the manage link in the plugins list 
         *
         * @access global
         * @return string The manage link in the plugins list 
         */
        function duplicator_manage_link($links, $file)
        {
            static $this_plugin;
            if (!$this_plugin)
                $this_plugin = plugin_basename(DUPLICATOR_LITE_FILE);

            if ($file == $this_plugin) {
                /*
                  $settings_link = '<a href="admin.php?page=duplicator">' . esc_html__("Manage", 'duplicator') . '</a>';
                  array_unshift($links, $settings_link);
                 */
                $upgrade_link = '<a href="https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=plugins_page&utm_campaign=duplicator_pro"><strong style="color: #11967A; display: inline;">' . esc_html__("Upgrade to Professional", 'duplicator') . '</strong></a>';
                array_unshift($links, $upgrade_link);
            }
            return $links;
        }

        /**
         * Adds links to the plugins manager page
         *
         * @access global
         * @return string The meta help link data for the plugins manager
         */
        function duplicator_meta_links($links, $file)
        {
            $plugin = plugin_basename(DUPLICATOR_LITE_FILE);
            // create link
            if ($file == $plugin) {
                $links[] = '<a href="admin.php?page=duplicator" title="' . esc_attr__('Manage Packages', 'duplicator') . '" style="">' . esc_html__('Manage', 'duplicator') . '</a>';
                return $links;
            }
            return $links;
        }
        /** ========================================================
         * GENERAL
         * =====================================================  */

        /**
         * Used for installer files to redirect if accessed directly
         *
         * @access global
         * @return null
         */
        function duplicator_secure_check()
        {
            $baseURL = "http://" . strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: $baseURL");
            exit;
        }
        if (!function_exists('duplicator_unhook_third_party_assets')) {

            /**
             * Remove all external styles and scripts coming from other plugins
             * which may cause compatibility issue, especially with React
             *
             * @return void
             */
            function duplicator_unhook_third_party_assets($hook)
            {
                /*
                  $hook values in duplicator admin pages:
                  toplevel_page_duplicator
                  duplicator_page_duplicator-tools
                  duplicator_page_duplicator-settings
                  duplicator_page_duplicator-gopro
                 */
                if (strpos($hook, 'duplicator') !== false && strpos($hook, 'duplicator-pro') === false) {
                    $unhook_third_party_js  = DUP_Settings::Get('unhook_third_party_js');
                    $unhook_third_party_css = DUP_Settings::Get('unhook_third_party_css');
                    $assets                 = array();
                    if ($unhook_third_party_css)
                        $assets['styles']       = wp_styles();
                    if ($unhook_third_party_js)
                        $assets['scripts']      = wp_scripts();
                    foreach ($assets as $type => $asset) {
                        foreach ($asset->registered as $handle => $dep) {
                            $src = $dep->src;
                            // test if the src is coming from /wp-admin/ or /wp-includes/ or /wp-fsqm-pro/.
                            if (
                                is_string($src) && // For some built-ins, $src is true|false
                                strpos($src, 'wp-admin') === false &&
                                strpos($src, 'wp-include') === false &&
                                // things below are specific to your plugin, so change them
                                strpos($src, 'duplicator') === false &&
                                strpos($src, 'woocommerce') === false &&
                                strpos($src, 'jetpack') === false &&
                                strpos($src, 'debug-bar') === false
                            ) {
                                'scripts' === $type ? wp_dequeue_script($handle) : wp_dequeue_style($handle);
                            }
                        }
                    }
                }
            }
        }
    }
}