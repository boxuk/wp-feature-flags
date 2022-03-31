<?php
/**
 * Uninstaller for the plugin.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

use BoxUk\WpFeatureFlags\FeatureManager;
use BoxUk\WpFeatureFlags\PluginUninstaller;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$autoloader = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $autoloader ) ) {
	require_once $autoloader; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
}

if ( ! class_exists( PluginUninstaller::class ) ) {
	return;
}

if ( ! defined( 'WP_FEATURE_FLAGS_PREFIX' ) ) {
	define( 'WP_FEATURE_FLAGS_PREFIX', 'boxuk' );
}

add_action(
	WP_FEATURE_FLAGS_PREFIX . '_plugin_uninstall',
	function () {
		$uninstaller = new PluginUninstaller( new FeatureManager() );
		$uninstaller->uninstall();
	}
);

do_action( WP_FEATURE_FLAGS_PREFIX . '_plugin_uninstall' );
