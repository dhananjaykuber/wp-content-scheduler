<?php
/**
 * Plugin Name:       WP Content Scheduler
 * Plugin URI:        https://github.com/dhananjaykuber/wp-content-scheduler.git
 * Description:       Allows scheduling post status changes at specific dates and times.
 * Version:           1.0
 * Author:            Dhananjay Kuber
 * Author URI:        https://github.com/dhananjaykuber
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       content-scheduler
 *
 * @package WP_Content_Scheduler
 */

namespace WP_Content_Scheduler;

use WP_Content_Scheduler\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the plugin version.
if ( ! defined( 'WP_CONTENT_SCHEDULER_VERSION' ) ) {
	define( 'WP_CONTENT_SCHEDULER_VERSION', '1.0' );
}

if ( ! defined( 'WP_CONTENT_SCHEDULER_PLUGIN_URL' ) ) {
	define( 'WP_CONTENT_SCHEDULER_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
}

if ( ! defined( 'WP_CONTENT_SCHEDULER_PLUGIN_DIR' ) ) {
	define( 'WP_CONTENT_SCHEDULER_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

require_once WP_CONTENT_SCHEDULER_PLUGIN_DIR . '/inc/classes/class-plugin.php';

/**
 * Initialize the plugin.
 *
 * @return void
 */
function init() {
	new Classes\Plugin();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\init' );
