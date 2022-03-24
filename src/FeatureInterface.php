<?php
/**
 * Interface that all features should adhere to.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare( strict_types=1 );

namespace BoxUk\WpPluginSkeleton;

interface FeatureInterface {
	/**
	 * Get the label of the feature. Should be a slugified string, e.g. example.
	 *
	 * @return string
	 */
	public static function get_label(): string;

	/**
	 * Whether a feature is enabled or not.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool;

	/**
	 * Init method for the feature, used for calling hooks for that feature.
	 *
	 * @return void
	 */
	public function init(): void;

	/**
	 * Uninstall method for the feature, used for uninstalling any feature specific data.
	 *
	 * @return void
	 */
	public function uninstall(): void;
}
