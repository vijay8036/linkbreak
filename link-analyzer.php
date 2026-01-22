<?php
/**
 * Plugin Name:       Link Analyzer - 404 Link Scanner
 * Plugin URI:        https://link-analyzer.com
 * Description:       A professional broken link scanner for WordPress. Scans posts, pages, and assets to detect 404 errors.
 * Version:           1.1.1
 * Author:            Vijay Salvi
 * License:           GPL-2.0+
 * Text Domain:       link-analyzer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// Define Constants
define('LINK_ANALYZER_VERSION', '1.1.1');
define('LINK_ANALYZER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LINK_ANALYZER_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_link_analyzer()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-link-analyzer-activator.php';
	Link_Analyzer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_link_analyzer()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-link-analyzer-deactivator.php';
	Link_Analyzer_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_link_analyzer');
register_deactivation_hook(__FILE__, 'deactivate_link_analyzer');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-link-analyzer.php';

/**
 * Begins execution of the plugin.
 */
function run_link_analyzer()
{
	$plugin = new Link_Analyzer();
	$plugin->run();
}
run_link_analyzer();
