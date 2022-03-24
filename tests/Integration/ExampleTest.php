<?php

declare( strict_types=1 );

namespace BoxUk\WpPluginSkeleton\Tests\Integration;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class ExampleTest extends TestCase {
	public function test_example(): void {
		self::assertEquals( 'http://example.org', get_home_url() );
	}
}
