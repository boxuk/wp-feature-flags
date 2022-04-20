<?php

declare( strict_types=1 );

namespace BoxUk\WpFeatureFlags\Tests\Unit;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use BoxUk\WpFeatureFlags\Flag\Flag;

class FlagTest extends TestCase {
	/**
	 * Tests the Flag class to confirm basic attributes are set as expected.
	 *
	 * @return void
	 */
	public function test_flag_attributes(): void {
		$default_flag = new Flag( 'test-flag', 'Test flag', 'This is a test flag' );

		self::assertEquals( 'test-flag', $default_flag->get_key() );
		self::assertEquals( 'Test flag', $default_flag->get_name() );
		self::assertEquals( 'This is a test flag', $default_flag->get_description() );
		self::assertEquals( [], $default_flag->get_meta() );
		self::assertEquals( 'All', $default_flag->get_group() );

		// Flags are not enforced or stable by default.
		self::assertFalse( $default_flag->is_enforced() );
		self::assertFalse( $default_flag->is_stable() );
	}

	/**
	 * Tests the Flag class to confirm meta attributes are set as expected.
	 *
	 * @return void
	 */
	public function test_flag_meta_attribute(): void {
		$meta_flag = new Flag(
			'meta-test',
			'Meta test',
			'This is a meta test flag',
			[
				'key' => 'value',
			]
		);

		self::assertEquals( [ 'key' => 'value' ], $meta_flag->get_meta() );
	}

	/**
	 * Tests the Flag class to confirm the group attribute is set as expected.
	 *
	 * @return void
	 */
	public function test_flag_group_attribute(): void {
		$group_flag = new Flag(
			'group-test',
			'Group test',
			'This is a group test flag',
			[],
			'Test group'
		);

		self::assertEquals( 'Test group', $group_flag->get_group() );
	}

	/**
	 * Tests the Flag class to confirm enforced status is set as expected.
	 *
	 * @return void
	 */
	public function test_flag_enforced_attribute(): void {
		$enforced_flag = new Flag(
			'enforced-test',
			'Enforced test',
			'This is an enforced test flag',
			[],
			'Test group',
			true
		);

		self::assertTrue( $enforced_flag->is_enforced() );
	}

	/**
	 * Tests the Flag class to confirm the stable attribute is set as expected.
	 *
	 * @return void
	 */
	public function test_flag_stable_attribute(): void {
		$stable_flag = new Flag(
			'stable-test',
			'Stable test',
			'This is a stable test flag',
			[],
			'Test group',
			false,
			true
		);

		self::assertTrue( $stable_flag->is_stable() );
	}

	/**
	 * Tests the Flag class to confirm the publish attribute is set as expected.
	 *
	 * @return void
	 */
	public function test_flag_enforced_is_enabled(): void {
		$enforced_flag = new Flag(
			'enabled-test',
			'Enabled test',
			'This is an enabled test flag',
			[],
			'Test group',
			true,
			true
		);

		self::assertTrue( $enforced_flag->is_published() );
	}

	/**
	 * Tests the Flag class to confirm the publish state is not true if the flag is unstable.
	 *
	 * @return void
	 */
	public function test_flag_unstable_is_not_published(): void {
		$enforced_flag = new Flag(
			'enabled-test',
			'Enabled test',
			'This is an enabled test flag',
			[],
			'Test group',
			true,
			false
		);

		self::assertFalse( $enforced_flag->is_published() );
	}
}
