<?php
/**
 * Test FlagRegister class
 *
 * @package BoxUk\WpFeatureFlags
 */

declare( strict_types = 1 );

namespace BoxUk\WpFeatureFlags;

use stdClass;
use WP_Mock\Tools\TestCase;

/**
 * FlagRegister class
 */
class TestFlagRegister extends TestCase {

	/**
	 * Test `instance` method
	 * 
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_instance() {
		$flag_register = FlagRegister::instance();
		$this->assertInstanceOf( FlagRegister::class, $flag_register );
		// Getting a second instance should return the same instance.
		$flag_register2 = FlagRegister::instance();
		$this->assertSame( $flag_register, $flag_register2 );
	}

	/**
	 * Test `get_flag` method
	 * 
	 * @param mixed $input Flag name.
	 * @param ?Flag $expected Expected flag.
	 * 
	 * @dataProvider data_provider_test_get_flag
	 * 
	 * @return void
	 */
	public function test_get_flag( $input, ?Flag $expected ): void {
		$flag_register = FlagRegister::instance();
		// Reset the register!
		foreach ( $flag_register->get_flags() as $flag ) {
			$flag_register->unregister_flag( $flag );
		}

		if ( $expected ) {
			$flag_register->register_flag( $expected );
		}

		$this->assertEquals( $expected, $flag_register->get_flag( $input ) );
	}

	/**
	 * Data provider for `test_get_flag` method
	 * 
	 * @return array<array<mixed>>
	 */
	public function data_provider_test_get_flag(): array {
		$flag_register = FlagRegister::instance();
		$flag          = new Flag( 'test', 'Test flag', 'Test flag description' );
		$flag_register->register_flag( $flag );

		return array(
			array( 'test', $flag ),
			array( $flag, $flag ),
			array( 'non-existent', null ),
		);
	}

	/**
	 * Test `get_flags` method
	 */
	public function test_get_flags(): void {
		$flag_register = FlagRegister::instance();
		$flag          = new Flag( 'test', 'Test flag', 'Test flag description' );
		$flag_register->register_flag( $flag );

		$this->assertEquals( array( 'test' => $flag ), $flag_register->get_flags() );
	}

	/**
	 * Test `register_flags` method
	 */
	public function test_register_flag(): void {
		$flag_register = FlagRegister::instance();
		$flag          = new Flag( 'test', 'Test flag', 'Test flag description' );
		$flag_2        = new Flag( 'test_2', 'Test flag 2', 'Test flag description 2' );

		$flag_register->register_flags( array( $flag, $flag_2 ) );

		$this->assertEquals(
			array(
				'test'   => $flag,
				'test_2' => $flag_2,
			),
			$flag_register->get_flags() 
		);
	}

	/**
	 * Test `sanitize_flags` method
	 */
	public function test_sanitize_flags(): void {
		$this->assertEquals( array(), Sanitizer::sanitize_flags( 'invalid input' ) );
		$this->assertEquals( array(), Sanitizer::sanitize_flags( array( new stdClass() ) ) );
	}
}   
