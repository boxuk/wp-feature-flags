<?php
/**
 * WP Feature Flags.
 *
 * @package BoxUk\WpFeatureFlags
 * @author Box UK
 * @copyright 2022 Box UK
 * @license GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: WP Feature Flags
 * Description: A plugin used to manage the publishing of features.
 * Author: Box UK
 * Author URI: https://www.boxuk.com/
 * Version: 0.3.3
 * License: GPLv3+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: boxuk
 * Domain Path: /languages/
 * Requires PHP: 8.0
 * Requires at least: 6.7
 * Tested up to: 6.7
 */

declare(strict_types=1);

use BoxUk\WpFeatureFlags\Plugin;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plugin_base_url = plugin_dir_url( __FILE__ );
define( 'WP_FEATURE_FLAGS_PLUGIN_URL', $plugin_base_url );

/**
 * Make sure we can access the autoloader, and it works.
 *
 * @return bool
 */
function wp_feature_flags_plugin_autoload(): bool {
 // phpcs:ignore NeutronStandard.Globals.DisallowGlobalFunctions.GlobalFunctions
	$autoloader = __DIR__ . '/vendor/autoload.php';
	if ( file_exists( $autoloader ) ) {
		require_once $autoloader; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
	}

	return class_exists( Plugin::class );
}

if ( ! wp_feature_flags_plugin_autoload() ) {
	return;
}

$app = new Plugin();

register_activation_hook( __FILE__, [ $app, 'activate' ] );
register_deactivation_hook( __FILE__, [ $app, 'deactivate' ] );

$app->run();
