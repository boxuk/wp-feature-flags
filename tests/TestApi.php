<?php
/**
 * Test Api class
 *
 * @package BoxUk\WpFeatureFlags
 */

declare( strict_types = 1 );

namespace BoxUk\WpFeatureFlags;

use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use WP_Error;
use WP_Mock\Tools\TestCase;
use WP_REST_Request;

/**
 * Api class
 */
class TestApi extends TestCase {

	/**
	 * Before test
	 */
	public function setUp(): void {
		parent::setUp();
	
		Mockery::getConfiguration()->setConstantsMap(
			array(
				'WP_REST_Server' => array(
					'READABLE' => 'GET',
					'EDITABLE' => 'POST',
				),
			)
		);

		Mockery::mock( 'alias:WP_REST_Controller' );
		Mockery::mock( 'alias:WP_REST_Response' );  
		Mockery::mock( 'alias:WP_REST_Server' );
	}

	/**
	 * Test `init` method
	 */
	public function test_init(): void {
		$api = new Api();
		\WP_Mock::expectActionAdded( 'rest_api_init', array( $api, 'register_routes' ) );
		$api->init();
		$this->assertConditionsMet();
	}

	/**
	 * Test `register_routes` method
	 */
	public function test_register_routes(): void {

		$api = new Api();

		\WP_Mock::userFunction(
			'register_rest_route',
			array(
				'times' => 1,
				'args'  => array(
					'feature-flags/v1',
					'/flags',
					array(
						'methods'             => 'GET',
						'callback'            => array( $api, 'get_items' ),
						'permission_callback' => array( $api, 'get_items_permissions_check' ),
					),
				),
			) 
		);

		\WP_Mock::userFunction(
			'register_rest_route',
			array(
				'times' => 1,
				'args'  => array(
					'feature-flags/v1',
					'/flags/(?P<flag>[a-zA-Z0-9_-]+)',
					array(
						'methods'             => 'POST',
						'callback'            => array( $api, 'update_item' ),
						'permission_callback' => array( $api, 'update_item_permissions_check' ),
					),
				),
			) 
		);

		
		$api->register_routes();
		$this->assertConditionsMet();
	}

	/**
	 * Test `get_items_permissions_check` method
	 * 
	 * @param bool $current_user_can Current user can.
	 * @param bool $expected Expected result.
	 * 
	 * @dataProvider data_provider_test_get_items_permissions_check
	 * 
	 * @return void
	 */
	public function test_get_items_permissions_check( bool $current_user_can, bool $expected ): void {
		$api = new Api();
		\WP_Mock::userFunction( 'current_user_can', array( 'return' => $current_user_can ) );

		if ( false === $expected ) { 
			$this->assertInstanceOf( WP_Error::class, $api->get_items_permissions_check( $this->get_mock_request() ) );
		} else { 
			$this->assertTrue( $api->get_items_permissions_check( $this->get_mock_request() ) );
		}
	}

	/**
	 * Test `update_item_permissions_check` method
	 *  
	 * @param bool $current_user_can Current user can.
	 * @param bool $expected Expected result.
	 * 
	 * @dataProvider data_provider_test_get_items_permissions_check
	 * 
	 * @return void
	 */
	public function test_update_item_permissions_check( bool $current_user_can, bool $expected ): void {
		$api = new Api();
		\WP_Mock::userFunction( 'current_user_can', array( 'return' => $current_user_can ) );

		if ( false === $expected ) { 
			$this->assertInstanceOf( WP_Error::class, $api->update_item_permissions_check( $this->get_mock_request() ) );
		} else { 
			$this->assertTrue( $api->update_item_permissions_check( $this->get_mock_request() ) );
		}
	}

	/**
	 * Data provider for `test_get_items_permissions_check` method
	 * 
	 * @return array<array<mixed>>
	 */
	public function data_provider_test_get_items_permissions_check(): array {
		return array(
			array( false, false ),
			array( true, true ),
		);
	}

	/**
	 * Test `get_items` method
	 * 
	 * @return void
	 */
	public function test_get_items(): void {
		$api = new Api();
		\WP_Mock::userFunction( 'rest_ensure_response' )->once()->andReturnArg( 0 );
		$this->assertIsArray( $api->get_items( $this->get_mock_request() ) );
	}

	/**
	 * Test `update_item` method
	 * 
	 * @param mixed      $flag_key Flag key.
	 * @param bool       $is_published Is published.
	 * @param array<int> $users Users.
	 * 
	 * @dataProvider data_provider_test_update_item
	 * 
	 * @return void
	 */
	public function test_update_item(
		mixed $flag_key,
		bool $is_published,
		array $users,
	): void {
		$mock_fr = Mockery::mock( FlagRegister::class );
		$api     = new Api( $mock_fr );
		
		$request = $this->get_mock_request();
		$request->shouldReceive( 'get_param' )->with( 'flag' )->andReturn( $flag_key );
		$request->shouldReceive( 'get_json_params' )->andReturn(
			array(
				'is_published' => $is_published,
				'users'        => $users,
			) 
		);
		
		if ( false === $flag_key ) { 
			\WP_Mock::userFunction( 'wp_die' )->once();
			$api->update_item( $request );
			$this->assertConditionsMet();
			return;
		}


		$mock_flag = 'invalid' !== $flag_key ? Mockery::mock( Flag::class ) : null;
		$mock_fr->shouldReceive( 'get_flag' )->with( $flag_key )->andReturn( $mock_flag );

		if ( null === $mock_flag ) { 
			\WP_Mock::userFunction( 'wp_die' )->once();
			$api->update_item( $request );
			$this->assertConditionsMet();
			return;
		}
		
		if ( $is_published ) { 
			$mock_flag->shouldReceive( 'publish' )->once();
			$mock_flag->shouldReceive( 'unpublish' )->never();
		} else {
			$mock_flag->shouldReceive( 'publish' )->never();
			$mock_flag->shouldReceive( 'unpublish' )->once();
		}
		
		$mock_flag->shouldReceive( 'get_users' )->andReturn( array( 3, 2, 1 ) );
		$mock_flag->shouldReceive( 'unpublish_for_user' )->with( Mockery::anyOf( 1, 2, 3 ) )->times( 3 );
		
		\WP_Mock::userFunction( 'absint' )->times( count( $users ) )->andReturnArg( 0 );
		foreach ( $users as $user ) {
			$mock_flag->shouldReceive( 'publish_for_user' )->with( $user )->once();
		}
		
		\WP_Mock::userFunction( 'rest_ensure_response' )->once()->andReturnArg( 0 );

		$api->update_item( $request );
		$this->assertConditionsMet();
	}

	/**
	 * Data provider for `test_update_item` method
	 * 
	 * @return array<array<mixed>>
	 */
	public function data_provider_test_update_item(): array {
		return array(
			array( false, true, array() ),
			array( 'invalid', true, array() ),
			array( 'test', true, array( 1, 2, 3 ) ),
			array( 'test', false, array( 1, 2, 3 ) ),
			array( 'test', true, array() ),
			array( 'test', false, array() ),
		);
	}

	/**
	 * Get Mock Request
	 * 
	 * @return LegacyMockInterface&MockInterface&WP_REST_Request
	 */
	private function get_mock_request() {
		return Mockery::mock( 'overload:' . WP_REST_Request::class );
	}
}
