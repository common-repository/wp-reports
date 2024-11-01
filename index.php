<?php

/*
 * Plugin Name: Reports for WordPress
 * Description: Powerful reporting framework for WordPress. Written by developers for developers.
 * Version: 1.0.12
 * Author: Dev Studio spol. s r.o.
 * Author URI: http://devstudio.sk/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

//  work out plugin folder name and store it as a constant
$plugin_dir = str_replace(basename(__FILE__), "", plugin_basename(__FILE__));
$plugin_dir = substr($plugin_dir, 0, strlen($plugin_dir) - 1);
define('DS_WP_REPORTS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DS_WP_REPORTS_PLUGIN_DIR', $plugin_dir);
define('DS_WP_REPORTS_PLUGIN_INDEX', __FILE__);

// enable I18N
load_plugin_textdomain('ds-wp-reports', false, dirname(plugin_basename(__FILE__)) . '/i18n/');

//	autoloader
function ds_wp_reports_autoloader($class) {

	$folders = array('lib', 'modules');
	foreach ($folders as $folder) {
		
		$pathToFile = DS_WP_REPORTS_PLUGIN_PATH . $folder . DIRECTORY_SEPARATOR . $class . '.php';
		if (file_exists($pathToFile)) {
			include $pathToFile;
		}
	}

}

spl_autoload_register('ds_wp_reports_autoloader');

DS_WP_Reports_Core::init();

//	activation & deactivation hooks (these MUST be in this file)
register_activation_hook(__FILE__, array('DS_WP_Reports_Core', 'onPluginActivation'));
