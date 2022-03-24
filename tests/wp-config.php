<?php
/**
 * WP Config file used for the functional tests.
 */

declare ( strict_types=1 );

use Symfony\Component\Dotenv\Dotenv;

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

if ( is_readable( __DIR__ . '/.env' ) ) {
	$dotenv = new Dotenv();
	$dotenv->usePutenv();
	$dotenv->load( __DIR__ . '/.env' );
}

/*
 * Path to the theme to test with.
 *
 * The 'default' theme is symlinked from test/phpunit/data/themedir1/default into
 * the themes directory of the WordPress installation defined above.
 */
define( 'WP_DEFAULT_THEME', 'default' );

// Test with multisite enabled.
// Alternatively, use the tests/phpunit/multisite.xml configuration file.
// define( 'WP_TESTS_MULTISITE', true );

// Force known bugs to be run.
// Tests with an associated Trac ticket that is still open are normally skipped.
// define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

// Prevent WP-Cron doing its thing during testing.
define( 'DISABLE_WP_CRON', true );

define( 'WP_PLUGIN_DIR', dirname( __DIR__, 2 ) );

// ** MySQL settings ** //

// This configuration file will be used by the copy of WordPress being tested.
// wordpress/wp-config.php will be ignored.

// WARNING WARNING WARNING!
// These tests will DROP ALL TABLES in the database with the prefix named below.
// DO NOT use a production database or one that is shared with something else.

define( 'DB_NAME', getenv( 'WP_TESTS_DB_NAME' ) ?: 'boxuk_wordpress_plugin_test' );
define( 'DB_USER', getenv( 'WP_TESTS_DB_USER' ) ?: 'root' );
define( 'DB_PASSWORD', getenv( 'WP_TESTS_DB_PASS' ) ?: '' );
define( 'DB_HOST', getenv( 'WP_TESTS_DB_HOST' ) ?: 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wp_';   // Only numbers, letters, and underscores please!

// Test suite configuration.
define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Box UK WordPress Plugin Tests' );
define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );
define( 'AUTH_SALT', 'abc123' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
