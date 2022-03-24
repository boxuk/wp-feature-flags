<?php
/**
 * Configuration for rector downgrading of newer PHP code.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare( strict_types=1 );

use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\DowngradeLevelSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function ( ContainerConfigurator $container_configurator ): void {
	$parameters = $container_configurator->parameters();

	$parameters->set(
		Option::PATHS,
		[
			__DIR__ . '/src',
			__DIR__ . '/tests',
		]
	);

	$parameters->set(
		Option::SKIP,
		[
			__DIR__ . '/tests/wp',
		]
	);

	$container_configurator->import( DowngradeLevelSetList::DOWN_TO_PHP_72 );
};
