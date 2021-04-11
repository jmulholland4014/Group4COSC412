<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

DUP_Handler::init_error_handler();
DUP_Util::hasCapability('export');

global $wpdb;

$dup_local_settings_path = dirname(__FILE__);

//COMMON HEADER DISPLAY
require_once(DUPLICATOR_PLUGIN_PATH . '/assets/js/javascript.php');
require_once(DUPLICATOR_PLUGIN_PATH . '/views/inc.header.php');
require_once(DUPLICATOR_PLUGIN_PATH . '/classes/ui/class.ui.dialog.php');
require_once(DUPLICATOR_PLUGIN_PATH . '/classes/ui/class.ui.messages.php');

$current_tab = isset($_REQUEST['tab']) ? sanitize_text_field($_REQUEST['tab']) : 'general';
?>

<div class="wrap">
    <?php duplicator_header(__("Settings", 'duplicator')) ?>

	<h2 class="nav-tab-wrapper">
        <a href="?page=duplicator-settings&tab=general" class="nav-tab <?php echo ($current_tab == 'general') ? 'nav-tab-active' : '' ?>"> <?php esc_html_e('General', 'duplicator'); ?></a>
		<a href="?page=duplicator-settings&tab=package" class="nav-tab <?php echo ($current_tab == 'package') ? 'nav-tab-active' : '' ?>"> <?php esc_html_e('Packages', 'duplicator'); ?></a>
		<a href="?page=duplicator-settings&tab=schedule" class="nav-tab <?php echo ($current_tab == 'schedule') ? 'nav-tab-active' : '' ?>"> <?php esc_html_e('Schedules', 'duplicator'); ?></a>
        <a href="?page=duplicator-settings&tab=storage" class="nav-tab <?php echo ($current_tab == 'storage') ? 'nav-tab-active' : '' ?>"> <?php esc_html_e('Storage', 'duplicator'); ?></a>
		<a href="?page=duplicator-settings&tab=license" class="nav-tab <?php echo ($current_tab == 'license') ? 'nav-tab-active' : '' ?>"> <?php esc_html_e('License', 'duplicator'); ?></a>
		<a href="?page=duplicator-settings&tab=about" class="nav-tab <?php echo ($current_tab == 'about') ? 'nav-tab-active' : '' ?>"> <?php esc_html_e('About', 'duplicator'); ?></a>
    </h2>

    <?php
    switch ($current_tab) {
        case 'general': include("{$dup_local_settings_path}/general.php");
            break;
		case 'package': include("{$dup_local_settings_path}/packages.php");
            break;
		case 'schedule': include("{$dup_local_settings_path}/schedule.php");
            break;
        case 'storage': include("{$dup_local_settings_path}/storage.php");
            break;
        case 'license': include("{$dup_local_settings_path}/license.php");
            break;
        case 'about': include("{$dup_local_settings_path}/about-info.php");
            break;
    }
    ?>
</div>