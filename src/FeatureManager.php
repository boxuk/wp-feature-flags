<?php
/**
 * The management and co-ordination of the main features of the plugin.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton;

use BoxUk\WpPluginSkeleton\ExampleFeature\ExampleFeature;

class FeatureManager {
	/**
	 * List of all the feature FQCNs.
	 *
	 * @var FeatureInterface[]
	 */
	private const FEATURES = [
		ExampleFeature::class,
	];

	/**
	 * Collection of features.
	 *
	 * @var FeatureInterface[]
	 */
	private $features = [];

	/**
	 * FeatureManager constructor.
	 */
	public function __construct() {
		$this->discover_features();
	}

	/**
	 * Loop over features and init them if they are enabled.
	 *
	 * @return void
	 */
	public function init_features(): void {
		foreach ( $this->features as $feature ) {
			if ( $feature->is_enabled() ) {
				$feature->init();
			}
		}
	}

	/**
	 * Loop over features and uninstall them.
	 *
	 * @return void
	 */
	public function uninstall_features(): void {
		foreach ( $this->features as $feature ) {
			$feature->uninstall();
		}
	}

	/**
	 * Check configured features implement the necessary interface and exist.
	 *
	 * @todo Use reflection to check if the class implements the interface.
	 * @return void
	 */
	private function discover_features(): void {
		foreach ( self::FEATURES as $feature ) {
			if ( class_exists( $feature ) ) {
				$feature_instance = new $feature(); // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
				if ( $feature_instance instanceof FeatureInterface ) {
					$this->features[] = $feature_instance;
				}
			}
		}
	}
}
