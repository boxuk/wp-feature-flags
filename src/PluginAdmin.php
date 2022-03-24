<?php
/**
 * Controller for the plugin Admin App.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton;

use BoxUk\WpPluginSkeleton\Admin\PluginSettings;

class PluginAdmin {
	/**
	 * Main run method for the plugin Admin app.
	 *
	 * @return void
	 */
	public function init(): void {
		( new PluginSettings() )->init();

		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_styles' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_scripts' ] );
	}

	/**
	 * Enqueue any CSS needed for the admin.
	 *
	 * @return void
	 */
	public static function enqueue_admin_styles(): void {
		wp_enqueue_style( BOXUK_PLUGIN_SKELETON_PREFIX . '-admin-styles', BOXUK_PLUGIN_SKELETON_PLUGIN_URL . 'assets/css/' . BOXUK_PLUGIN_SKELETON_PREFIX . '-admin.css', [], BOXUK_PLUGIN_SKELETON_VERSION );
		wp_enqueue_style( BOXUK_PLUGIN_SKELETON_PREFIX . '-dashicons', BOXUK_PLUGIN_SKELETON_PLUGIN_URL . 'assets/css/' . BOXUK_PLUGIN_SKELETON_PREFIX . '-icon.css', [], BOXUK_PLUGIN_SKELETON_VERSION );
	}

	/**
	 * Enqueue any JS needed for the admin.
	 *
	 * @return void
	 */
	public static function enqueue_admin_scripts(): void {
		wp_enqueue_script( BOXUK_PLUGIN_SKELETON_PREFIX . '-admin-script', BOXUK_PLUGIN_SKELETON_PLUGIN_URL . 'assets/js/' . BOXUK_PLUGIN_SKELETON_PREFIX . '-admin.js', [], BOXUK_PLUGIN_SKELETON_VERSION, true );
	}
}
