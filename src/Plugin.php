<?php
/**
 * Main Controller for the plugin.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags;

class Plugin {
	/**
	 * FeatureManager instance.
	 *
	 * @var FeatureManager
	 */
	private $feature_manager;

	/**
	 * Constructor for the plugin.
	 *
	 * @param FeatureManager $feature_manager FeatureManager instance.
	 */
	public function __construct( FeatureManager $feature_manager ) {
		$this->feature_manager = $feature_manager;
	}

	/**
	 * Main run method for the plugin.
	 *
	 * @return void
	 */
	public function run(): void {
		if ( is_admin() ) {
			$admin_app = new PluginAdmin();
			$admin_app->init();
		}

		$this->feature_manager->init_features();

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_styles' ] );
			add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
		}
	}

	/**
	 * Enqueue any CSS needed for the frontend.
	 *
	 * @return void
	 */
	public static function enqueue_styles(): void {
		wp_enqueue_style( WP_FEATURE_FLAGS_PREFIX . '-frontend-styles', WP_FEATURE_FLAGS_PLUGIN_URL . 'assets/css/' . WP_FEATURE_FLAGS_PREFIX . '-frontend.css', [], WP_FEATURE_FLAGS_VERSION );
	}

	/**
	 * Enqueue any CSS needed for the frontend.
	 *
	 * @return void
	 */
	public static function enqueue_scripts(): void {
		wp_enqueue_style( WP_FEATURE_FLAGS_PREFIX . '-frontend-scripts', WP_FEATURE_FLAGS_PLUGIN_URL . 'assets/js/' . WP_FEATURE_FLAGS_PREFIX . '-frontend.js', [], WP_FEATURE_FLAGS_VERSION );
	}
}
