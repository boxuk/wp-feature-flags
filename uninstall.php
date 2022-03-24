<?php
/**
 * Uninstaller for the plugin.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

use BoxUk\WpPluginSkeleton\FeatureManager;
use BoxUk\WpPluginSkeleton\PluginUninstaller;

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

if ( ! defined( 'BOXUK_PLUGIN_SKELETON_PREFIX' ) ) {
	define( 'BOXUK_PLUGIN_SKELETON_PREFIX', 'boxuk' );
}

add_action(
	BOXUK_PLUGIN_SKELETON_PREFIX . '_plugin_uninstall',
	function () {
		$uninstaller = new PluginUninstaller( new FeatureManager() );
		$uninstaller->uninstall();
	}
);

do_action( BOXUK_PLUGIN_SKELETON_PREFIX . '_plugin_uninstall' );
