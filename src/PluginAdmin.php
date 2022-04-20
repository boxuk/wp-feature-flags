<?php
/**
 * Controller for the plugin Admin App.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags;

use BoxUk\WpFeatureFlags\Admin\PluginSettings;

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
		wp_enqueue_style( WP_FEATURE_FLAGS_PREFIX . '-admin-styles', WP_FEATURE_FLAGS_PLUGIN_URL . 'assets/css/' . WP_FEATURE_FLAGS_PREFIX . '-admin.css', [], WP_FEATURE_FLAGS_VERSION );
		wp_enqueue_style( WP_FEATURE_FLAGS_PREFIX . '-dashicons', WP_FEATURE_FLAGS_PLUGIN_URL . 'assets/css/' . WP_FEATURE_FLAGS_PREFIX . '-icon.css', [], WP_FEATURE_FLAGS_VERSION );
	}

	/**
	 * Enqueue any JS needed for the admin.
	 *
	 * @return void
	 */
	public static function enqueue_admin_scripts(): void {
		wp_enqueue_script( WP_FEATURE_FLAGS_PREFIX . '-admin-script', WP_FEATURE_FLAGS_PLUGIN_URL . 'assets/js/' . WP_FEATURE_FLAGS_PREFIX . '-admin.js', [], WP_FEATURE_FLAGS_VERSION, true );

		wp_localize_script(
			WP_FEATURE_FLAGS_PREFIX . '-admin-script',
			'WPFFAdmin',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'secret' => wp_create_nonce( 'wp_feature_flags_admin_secret' ),
				'i18n' => [
					'publish' => __( 'Publish', 'wp-feature-flags' ),
					'unpublish' => __( 'Unpublish', 'wp-feature-flags' ),
					'on' => __( 'On', 'wp-feature-flags' ),
					'off' => __( 'Off', 'wp-feature-flags' ),
				],
			]
		);
	}
}
