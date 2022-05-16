<?php

declare( strict_types=1 );

namespace BoxUk\WpFeatureFlags\Tests\Unit;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use BoxUk\WpFeatureFlags\Flag\Flag;
use BoxUk\WpFeatureFlags\FlagRegister\FlagRegister;

class FlagRegisterTest extends TestCase {
	// convert_register_to_flag_class

	/**
	 * Tests get_all_registered_flags returns an empty array by default.
	 *
	 * @return void
	 */
	public function test_get_all_registered_flags_when_empty(): void {
		$flag_register = new FlagRegister();

		self::assertEquals( [], $flag_register->get_all_registered_flags() );
	}

	// get_unique_groups

	// get_published_flags

	// get_preview_flags

	/**
	 * Tests get_flag_by_key returns expected registered flag.
	 *
	 * @return void
	 */
	public function test_get_flag_by_key_returns_registered_flag(): void {
		$flag_register = new FlagRegister();
		$flag_register->set_registered_flag( 'test-flag', 'Test flag', 'This is a test flag' );

		self::assertEquals(
			new Flag( 'test-flag', 'Test flag', 'This is a test flag' ),
			$flag_register->get_flag_by_key( 'test-flag' )
		);
	}

	// get_flags_by_group

	// set_all_flag_defaults

	public function test_set_flag_defaults_sets_expected_default_values(): void {
		$flag_register = new FlagRegister();
		$flag_arr = [
			'key' => 'test-flag',
			'name' => 'Test flag',
			'description' => 'This is a test flag',
		];

		$flag_with_defaults = $flag_register->set_flag_defaults( $flag_arr );
		$expected_result = [
			'key' => 'test-flag',
			'name' => 'Test flag',
			'description' => 'This is a test flag',
			'meta' => [],
			'group' => 'All',
			'enforced' => false,
			'stable' => false,
			'parent' => '',
		];

		self::assertEquals( $expected_result, $flag_with_defaults );
	}

	public function test_set_registered_flag_adds_flag_to_register(): void {
		$flag_register = new FlagRegister();
		$flag_register->set_registered_flag( 'test-flag', 'Test flag', 'This is a test flag' );

		self::assertEquals(
			[
				'test-flag' => [
					'key' => 'test-flag',
					'name' => 'Test flag',
					'description' => 'This is a test flag',
					'meta' => [],
					'group' => 'All',
					'enforced' => false,
					'stable' => false,
					'parent' => '',
				],
			],
			$flag_register->get_all_registered_flags()
		);
	}

	// set_published_flags

	// set_flag_publish_status_batch

	// set_flag_publish_status

	// update_published_flags

	// set_flag_preview_status

	// update_preview_flags
}
