<?php

declare( strict_types=1 );

namespace BoxUk\WpFeatureFlags\Tests\Integration;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use BoxUk\WpFeatureFlags\DatabaseManager\DatabaseManager;

class ExampleTest extends TestCase {
	public function test_add_database_option_if_missing_adds_option(): void {
		$test_option = 'test_option';

		$database_manager = new DatabaseManager();
		$database_manager->add_database_option_if_missing( $test_option );

		self::assertEquals( [], maybe_unserialize( get_option( $test_option ) ) );
	}

	public function test_add_user_meta_if_missing_adds_user_meta() {
		$user_id = 1;
		$meta_key = 'test_meta_key';

		$database_manager = new DatabaseManager();
		$database_manager->add_user_meta_if_missing( $meta_key, $user_id );
		$user_meta = get_user_meta( $user_id, $meta_key, true );

		self::assertEquals( [], maybe_unserialize( $user_meta ) );
	}

	/**
	 * Test get_published_flags returns an empty array by default.
	 *
	 * @return void
	 */
	public function test_get_published_flags_returns_empty_array(): void {
		$database_manager = new DatabaseManager();

		self::assertEquals( [], $database_manager->get_published_flags() );
	}

	/**
	 * Test get_published_flags returns an empty array by default.
	 *
	 * @return void
	 */
	public function test_get_preview_flags_returns_empty_array(): void {
		$database_manager = new DatabaseManager();

		self::assertEquals( [], $database_manager->get_preview_flags( 1 ) );
	}

	/**
	 * Tests set_published_flags returns the expected array of flags.
	 * Also tests the flags are changed when set_published_flags is used again.
	 *
	 * @return void
	 */
	public function test_set_published_flags_sets_published_flags(): void {
		$test_option = 'wp_feature_flags_published_flags';
		$test_flags = [ 'test_flag', 'test-flag', 'flag!test' ];

		$database_manager = new DatabaseManager();
		$database_manager->set_published_flags( $test_flags );

		self::assertEquals( $test_flags, maybe_unserialize( get_option( $test_option ) ) );

		$database_manager->set_published_flags( [ 'second-test' ] );
		self::assertEquals( [ 'second-test' ] , maybe_unserialize( get_option( $test_option ) ) );
	}

	/**
	 * Tests set_preview_flags returns the expected array of flags.
	 * Also tests the flags are changed when set_preview_flags is used again.
	 *
	 * @return void
	 */
	public function test_set_preview_flags_sets_preview_flags(): void {
		$preview_meta_key = 'wp_feature_flags_previewing_flags';
		$user_id = 1;
		$preview_flags = [ 'test_flag', 'test-flag-2', '3-flag-test' ];

		$database_manager = new DatabaseManager();
		$database_manager->set_preview_flags( $preview_flags, $user_id );

		self::assertEquals( $preview_flags, maybe_unserialize( get_user_meta( 1, $preview_meta_key, true ) ) );

		$database_manager->set_preview_flags( [ 'test-flag' ], $user_id );
		self::assertEquals( [ 'test-flag' ], maybe_unserialize( get_user_meta( 1, $preview_meta_key, true ) ) );
	}

	/**
	 * Tests set_published_flags returns the expected array of flags using get_published_flags.
	 *
	 * @return void
	 */
	public function test_get_published_flags_returns_set_published_flags_input(): void {
		$test_option = 'wp_feature_flags_published_flags';
		$test_flags = [ 'test_flag', 'test-flag', 'flag!test' ];

		$database_manager = new DatabaseManager();
		$database_manager->set_published_flags( $test_flags );

		self::assertEquals( $test_flags, $database_manager->get_published_flags() );

		$database_manager->set_published_flags( [ 'second-test' ] );

		self::assertEquals( [ 'second-test' ], $database_manager->get_published_flags() );
	}

	/**
	 * Tests set_published_flags returns the expected array of flags using get_published_flags.
	 *
	 * @return void
	 */
	public function test_get_preview_flags_returns_set_preview_flags_input(): void {
		$user_id = 1;
		$test_flags = [ 'testingflags', 'test0flag', 'flag#test' ];

		$database_manager = new DatabaseManager();
		$database_manager->set_preview_flags( $test_flags, $user_id );

		self::assertEquals( $test_flags, $database_manager->get_preview_flags( $user_id ) );

		$database_manager->set_preview_flags( [ 'preview-second-test' ], $user_id );

		self::assertEquals( [ 'preview-second-test' ], $database_manager->get_preview_flags( $user_id ) );
	}
}
