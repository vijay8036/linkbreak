<?php
/**
 * Plugin Name:       LinkBrack - 404 Link Scanner
 * Plugin URI:        https://linkbrack.com
 * Description:       A professional broken link scanner for WordPress. Scans posts, pages, and assets to detect 404 errors.
 * Version:           1.0.5
 * Author:            Antigravity
 * Author URI:        https://google.com
 * License:           GPL-2.0+
 * Text Domain:       linkbrack
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define Constants
define( 'LINKBRACK_VERSION', '1.0.5' );
define( 'LINKBRACK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LINKBRACK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_linkbrack() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-linkbrack-activator.php';
	LinkBrack_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_linkbrack() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-linkbrack-deactivator.php';
	LinkBrack_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_linkbrack' );
register_deactivation_hook( __FILE__, 'deactivate_linkbrack' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-linkbrack.php';

/**
 * Begins execution of the plugin.
 */
function run_linkbrack() {
	$plugin = new LinkBrack();
	$plugin->run();
}
run_linkbrack();
