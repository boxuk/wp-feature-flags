<?php
/**
 * Example feature class.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton\ExampleFeature;

use BoxUk\WpPluginSkeleton\FeatureInterface;

final class ExampleFeature implements FeatureInterface {
	private const FEATURE_LABEL = 'example';

	/**
	 * Label for this feature.
	 *
	 * @return string
	 */
	public static function get_label(): string {
		return self::FEATURE_LABEL;
	}

	/**
	 * Whether the feature is enabled. This could come from a setting value.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return true;
	}

	/**
	 * Init method to initialise the feature.
	 *
	 * @return void
	 */
	public function init(): void {
		// Do something.
	}

	/**
	 * Uninstall method to remove any data created by the feature.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		( new ExampleFeatureUninstaller() )->uninstall();
	}
}
