<?php

declare( strict_types=1 );

namespace BoxUk\WpFeatureFlags\Tests\Integration;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use BoxUk\WpFeatureFlags\Flag\Flag;

class FlagTest extends TestCase {
	/**
	 * Publish a flag via the database and test it returns true for 'is_published'.
	 *
	 * @return void
	 */
	public function test_flag_is_published(): void {
		update_option( 'wp_feature_flags_published_flags', maybe_serialize( [ 'test_flag' ] ) );

		$published_flag = new Flag(
			'test-flag',
			'Test flag',
			'This is a test flag',
			[],
			'Test group',
			false,
			true
		);

		self::assertEquals(
			[ 'test_flag' ],
			maybe_unserialize( get_option( 'wp_feature_flags_published_flags' ) )
		);
	}
}
