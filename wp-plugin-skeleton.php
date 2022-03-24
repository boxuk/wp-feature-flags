<?php
/**
 * Box UK WordPress Plugin.
 *
 * @package BoxUk\WpPluginSkeleton
 * @author Box UK
 * @copyright 2022 Box UK
 * @license GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Box UK WordPress Plugin Skeleton.
 * Description: A skeleton WordPress plugin to be used as a base for new WordPress plugins.
 * Author: Box UK
 * Author URI: https://www.boxuk.com/
 * Version: 1.0.0
 * License: GPLv3+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: boxuk
 * Domain Path: /languages/
 * Requires PHP: 7.2
 * Requires at least: 5.0
 * Tested up to: 5.8
 */

declare ( strict_types=1 );

use BoxUk\WpPluginSkeleton\Activation;
use BoxUk\WpPluginSkeleton\FeatureManager;
use BoxUk\WpPluginSkeleton\Plugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	exit;
}

define( 'BOXUK_PLUGIN_SKELETON_VERSION', '1.0.0' );
define( 'BOXUK_PLUGIN_SKELETON_PREFIX', 'boxuk' );

$plugin_base_url = plugin_dir_url( __FILE__ );
define( 'BOXUK_PLUGIN_SKELETON_PLUGIN_URL', $plugin_base_url );

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
