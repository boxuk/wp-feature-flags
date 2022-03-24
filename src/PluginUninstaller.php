<?php
/**
 * Uninstaller for the plugin.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton;

class PluginUninstaller {
	/**
	 * FeatureManager instance.
	 *
	 * @var FeatureManager
	 */
	private $feature_manager;

	/**
	 * Constructor for PluginUninstaller.
	 *
	 * @param FeatureManager $feature_manager FeatureManager instance.
	 */
	public function __construct( FeatureManager $feature_manager ) {
		$this->feature_manager = $feature_manager;
	}

	/**
	 * Main uninstall method.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		$this->feature_manager->uninstall_features();
		$this->delete_settings();
		wp_cache_flush();
		flush_rewrite_rules(); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules
	}

	/**
	 * Delete settings.
	 *
	 * @return void
	 */
	private function delete_settings(): void {
		global $wpdb;

		$plugin_prefix = BOXUK_PLUGIN_SKELETON_PREFIX;
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$plugin_prefix}_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}
