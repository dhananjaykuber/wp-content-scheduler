<?php
/**
 * Bootstrap the plugin.
 *
 * @package WP_Content_Scheduler
 */

namespace WP_Content_Scheduler\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Content_Scheduler\Classes\Metabox;
use WP_Content_Scheduler\Classes\Cron;

require_once WP_CONTENT_SCHEDULER_PLUGIN_DIR . '/inc/classes/class-metabox.php';
require_once WP_CONTENT_SCHEDULER_PLUGIN_DIR . '/inc/classes/class-cron.php';
require_once WP_CONTENT_SCHEDULER_PLUGIN_DIR . '/inc/classes/class-posts-column.php';
require_once WP_CONTENT_SCHEDULER_PLUGIN_DIR . '/inc/classes/class-settings.php';

/**
 * Plugin Class.
 */
class Plugin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		new Metabox();
		new Cron();
		new Posts_Column();
		new Settings();
	}
}
