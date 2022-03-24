<?php
/**
 * PHPUnit bootstrap file
 */

declare ( strict_types=1 );

// Composer autoloader must be loaded before WP_PHPUNIT__DIR will be available
use Symfony\Component\Dotenv\Dotenv;

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

if ( is_readable( __DIR__ . '/.env' ) ) {
	$dotenv = new Dotenv();
	$dotenv->usePutenv();
	$dotenv->load( __DIR__ . '/.env' );
}

$is_integration = isset( $GLOBALS['argv'][1]) && str_contains( $GLOBALS['argv'][1], 'integration' );

if ( ! $is_integration ) {
	// Mock translation function for unit tests.
	if ( ! function_exists( '__' ) ) {
		function __( string $string, string $textdomain = 'default' ): string {
			return $string;
		}
	}
	return;
}

$tests_dir = getenv('WP_PHPUNIT__DIR');

// Give access to tests_add_filter() function.
require_once $tests_dir . '/includes/functions.php';

tests_add_filter( 'muplugins_loaded', static function() {
	// test set up, plugin activation, etc.
	require dirname( __DIR__ ) . '/wp-plugin-skeleton.php';
} );

// Start up the WP testing environment.
require $tests_dir . '/includes/bootstrap.php';
