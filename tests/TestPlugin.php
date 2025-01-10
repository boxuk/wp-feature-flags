<?php
/**
 * Test Plugin class
 *
 * @package BoxUk\WpFeatureFlags
 */

declare( strict_types = 1 );

namespace BoxUk\WpFeatureFlags;

use Mockery;
use WP_Mock\Tools\TestCase;

/**
 * Plugin class
 * 
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TestPlugin extends TestCase {

	/**
	 * Test Run
	 */
	public function test_run(): void {
		$mock_api        = Mockery::mock( 'overload:' . Api::class );
		$mock_admin_page = Mockery::mock( 'overload:' . AdminPage::class );
		$mock_api->shouldReceive( 'init' )->once();
		$mock_admin_page->shouldReceive( 'init' )->once();

		$plugin = new Plugin();     
		$plugin->run();
		$this->assertConditionsMet();
	}

	/**
	 * Test `activate` method
	 */
	public function test_activate(): void {
		Mockery::mock( 'overload:' . Api::class );
		Mockery::mock( 'overload:' . AdminPage::class );

		$plugin = new Plugin();
		$plugin->activate();

		$this->assertConditionsMet();
	}

	/**
	 * Test `deactivate` method
	 */
	public function test_deactivate(): void {
		Mockery::mock( 'overload:' . Api::class );
		Mockery::mock( 'overload:' . AdminPage::class );
		
		$plugin = new Plugin();

		\WP_Mock::userFunction( 'delete_option' )->once()->with( 'wp_feature_flags_published_flags' );
		\WP_Mock::userFunction( 'get_users' )->once()->with( array( 'fields' => 'ID' ) )->andReturn( array( 1, 2, 3 ) );
		\WP_Mock::userFunction( 'delete_user_meta' )->times( 3 )->with( Mockery::type( 'int' ), 'wp_feature_flags_user_flags' );
		\WP_Mock::userFunction( 'wp_cache_flush' )->once();
		
		$plugin->deactivate();
		$this->assertConditionsMet();
	}
}
