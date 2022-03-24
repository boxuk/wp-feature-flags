<?php
/**
 * Class for accessing plugin settings.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton;

class PluginSettingsManager {
	/**
	 * Check if the plugin is enabled. Defaults to true.
	 *
	 * @return bool
	 */
	public static function is_enabled(): bool {
		return filter_var( get_option( BOXUK_PLUGIN_SKELETON_PREFIX . '_enable_plugin', '1' ), FILTER_VALIDATE_BOOLEAN );
	}
}
