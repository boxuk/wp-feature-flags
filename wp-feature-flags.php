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
 * Version: 0.1
 * License: GPLv3+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: boxuk
 * Domain Path: /languages/
 * Requires PHP: 7.2
 * Requires at least: 5.0
 * Tested up to: 5.8
 */

declare ( strict_types=1 );

use BoxUk\WpFeatureFlags\Activation;
use BoxUk\WpFeatureFlags\FeatureManager;
use BoxUk\WpFeatureFlags\Plugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	exit;
}

define( 'WP_FEATURE_FLAGS_VERSION', '0.1' );
define( 'WP_FEATURE_FLAGS_PREFIX', 'wp-feature-flags' );

$plugin_base_url = plugin_dir_url( __FILE__ );
define( 'WP_FEATURE_FLAGS_PLUGIN_URL', $plugin_base_url );

/**
 * Make sure we can access the autoloader, and it works.
 *
 * @return bool
 */
function boxuk_plugin_autoload(): bool { // phpcs:ignore NeutronStandard.Globals.DisallowGlobalFunctions.GlobalFunctions
	$autoloader = __DIR__ . '/vendor/autoload.php';
	if ( file_exists( $autoloader ) ) {
		require_once $autoloader; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
	}

	return class_exists( Plugin::class );
}

if ( ! boxuk_plugin_autoload() ) {
	return;
}

register_activation_hook( __FILE__, [ Activation::class, 'activate' ] );

$app = new Plugin( new FeatureManager() );
$app->run();
