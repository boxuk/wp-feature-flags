<?php
/**
 * Activation tasks for the plugin.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags;

use BoxUk\WpFeatureFlags\DatabaseManager\DatabaseManager;

class Activation {
	/**
	 * Run any tasks we need at activation.
	 *
	 * @return void
	 */
	public static function activate(): void {
		$database_manager = new DatabaseManager();
		$database_manager->init();
	}
}
