<?php
/**
 * Plugin Name: Sticklets
 * Plugin URI: https://defoix.com/sticklets
 * Description: Add floating sticker images to your site with advanced visibility triggers
 * Version: 1.0.1
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Marc Deep Foix
 * Author URI: https://defoix.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sticklets
 * Domain Path: /languages
 * Update URI: https://defoix.com/sticklets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STICKLETS_VERSION', '1.0.1' );
define( 'STICKLETS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STICKLETS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'STICKLETS_PLUGIN_FILE', __FILE__ );

require_once STICKLETS_PLUGIN_DIR . 'includes/class-sticklets.php';

function sticklets_init() {
	$plugin = new Sticklets();
	$plugin->run();
}
add_action( 'plugins_loaded', 'sticklets_init' );