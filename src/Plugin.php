<?php
/**
 * Main Controller for the plugin.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton;

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
		wp_enqueue_style( BOXUK_PLUGIN_SKELETON_PREFIX . '-frontend-styles', BOXUK_PLUGIN_SKELETON_PLUGIN_URL . 'assets/css/' . BOXUK_PLUGIN_SKELETON_PREFIX . '-frontend.css', [], BOXUK_PLUGIN_SKELETON_VERSION );
	}

	/**
	 * Enqueue any CSS needed for the frontend.
	 *
	 * @return void
	 */
	public static function enqueue_scripts(): void {
		wp_enqueue_style( BOXUK_PLUGIN_SKELETON_PREFIX . '-frontend-scripts', BOXUK_PLUGIN_SKELETON_PLUGIN_URL . 'assets/js/' . BOXUK_PLUGIN_SKELETON_PREFIX . '-frontend.js', [], BOXUK_PLUGIN_SKELETON_VERSION );
	}
}
