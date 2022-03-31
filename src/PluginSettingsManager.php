<?php
/**
 * Class for accessing plugin settings.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags;

class PluginSettingsManager {
	/**
	 * Check if the plugin is enabled. Defaults to true.
	 *
	 * @return bool
	 */
	public static function is_enabled(): bool {
		return filter_var( get_option( WP_FEATURE_FLAGS_PREFIX . '_enable_plugin', '1' ), FILTER_VALIDATE_BOOLEAN );
	}
}
