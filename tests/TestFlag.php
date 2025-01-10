<?php
/**
 * Test Flag class
 *
 * @package BoxUk\WpFeatureFlags
 */

declare( strict_types = 1 );

namespace BoxUk\WpFeatureFlags;

use DateTime;
use Mockery;
use WP_Mock\Tools\TestCase;

/**
 * Flag class
 */
class TestFlag extends TestCase {

	/**
	 * Get Flag
	 * 
	 * @param DateTime $dt Date time.
	 * 
	 * @return Flag
	 */
	private function get_flag( ?DateTime $dt = null ): Flag {
		return new Flag( 'test', 'Test Flag', 'This is a test flag', $dt );
	}

	/**
	 * Test `get_key` method
	 */
	public function test_get_key(): void {
		$this->assertEquals( 'test', $this->get_flag()->get_key() );
	}

	/**
	 * Test `get_name` method
	 */
	public function test_get_name(): void {
		$this->assertEquals( 'Test Flag', $this->get_flag()->get_name() );
	}

	/**
	 * Test `get_description` method
	 */
	public function test_get_description(): void {
		$this->assertEquals( 'This is a test flag', $this->get_flag()->get_description() );
	}

	/**
	 * Test `get_created` method
	 */
	public function test_get_created(): void {
		$dt = new DateTime();
		$this->assertEquals( $dt, $this->get_flag( $dt )->get_created() );
	}

	/**
	 * Test `get_group` method
	 */
	public function test_get_group(): void {
		$this->assertEquals( 'Default', $this->get_flag()->get_group() );
	}

	/**
	 * Test `is_enforced` method
	 */
	public function test_is_enforced(): void {
		$this->assertFalse( $this->get_flag()->is_force_enabled() );
	}

	/**
	 * Test `is_force_disabled` method
	 */
	public function test_is_force_disabled(): void {
		$this->assertFalse( $this->get_flag()->is_force_disabled() );
	}

	/**
	 * Test `can_be_published` method
	 */
	public function test_can_be_published(): void {
		$flag = new Flag( 'test', 'Test Flag', 'This is a test flag', null, 'Default', false, false );
		$this->assertTrue( $flag->can_be_published() );

		$flag = new Flag( 'test', 'Test Flag', 'This is a test flag', null, 'Default', true, true );
		$this->assertFalse( $flag->can_be_published() );

		$flag = new Flag( 'test', 'Test Flag', 'This is a test flag', null, 'Default', true, false );
		$this->assertFalse( $flag->can_be_published() );
	}

	/**
	 * Test `is_published` method
	 */
	public function test_is_published(): void {
		$flag = $this->get_flag();
		FlagRegister::instance()->register_flag( $flag );
		\WP_Mock::userFunction( 'get_option' )
			->with( Flag::FLAG_PUBLISH_OPTION )
			->andReturn( array( $flag->get_key() ) );

		$this->assertTrue( $flag->is_published() );
		FlagRegister::instance()->unregister_flag( $flag );
	}

	/**
	 * Test `get_users` method
	 */
	public function test_get_users(): void {
		\WP_Mock::userFunction( 'get_users' )
			->once()
			->with( array( 'fields' => 'ID' ) )
			->andReturn( array( 0, 1, 2, 3 ) );

		\WP_Mock::userFunction( 'did_action' )->once()->with( 'init' )->andReturn( true );
		\WP_Mock::userFunction( 'get_current_user_id' )->once()->andReturn( 1 );

		\WP_Mock::userFunction( 'absint' )->andReturnArg( 0 );

		\WP_Mock::userFunction( 'get_user_meta' )
			->with( \Mockery::anyOf( 1, 2, 3 ), 'wp_feature_flags_user_flags', true )
			->andReturn( array( $this->get_flag()->get_key() ) );

		$flag = $this->get_flag();
		FlagRegister::instance()->register_flag( $flag );

		$this->assertEquals( array( 0, 1, 2, 3 ), $flag->get_users() );

		FlagRegister::instance()->unregister_flag( $flag );
	}

	/**
	 * Test `get_users` method with invalid meta
	 */
	public function test_get_users_invalid_meta(): void {
		\WP_Mock::userFunction( 'get_users' )
			->once()
			->with( array( 'fields' => 'ID' ) )
			->andReturn( array( 0, 1, 2, 3 ) );

		\WP_Mock::userFunction( 'did_action' )->once()->with( 'init' )->andReturn( true );
		\WP_Mock::userFunction( 'get_current_user_id' )->once()->andReturn( 1 );

		\WP_Mock::userFunction( 'absint' )->andReturnArg( 0 );

		\WP_Mock::userFunction( 'get_user_meta' )
			->with( \Mockery::anyOf( 1, 2, 3 ), 'wp_feature_flags_user_flags', true )
			->andReturn( 'invalid' );
		\WP_Mock::userFunction( 'add_user_meta' )
			->with( \Mockery::anyOf( 1, 2, 3 ), 'wp_feature_flags_user_flags', array(), true )
			->andReturn( true );

		$flag = $this->get_flag();
		FlagRegister::instance()->register_flag( $flag );

		$this->assertEquals( array(), $flag->get_users() );

		FlagRegister::instance()->unregister_flag( $flag );
	}

	/**
	 * Test `is_enabled_for_user` method
	 */
	public function test_is_enabled_for_user(): void {
		\WP_Mock::userFunction( 'get_user_meta' )->once()
			->with( 1, 'wp_feature_flags_user_flags', true )
			->andReturn( array( $this->get_flag()->get_key() ) );
		
		$flag = $this->get_flag();
		FlagRegister::instance()->register_flag( $flag );

		$this->assertTrue( $flag->is_enabled_for_user( 1 ) );

		FlagRegister::instance()->unregister_flag( $flag );
	}

	/**
	 * Test `is_enabled` method
	 * 
	 * @dataProvider is_enabled_provider
	 * 
	 * @param Flag  $flag     Flag.
	 * @param array $option   Option.
	 * @param array $users    Users.
	 * @param bool  $expected Expected.
	 */
	public function test_is_enabled( Flag $flag, array $option = array(), array $users = array(), bool $expected = false ): void {
		FlagRegister::instance()->register_flag( $flag );
		\WP_Mock::userFunction( 'get_option' )
			->with( Flag::FLAG_PUBLISH_OPTION )
			->andReturn( $option );

		\WP_Mock::userFunction( 'get_user_meta' )
			->with( 1, 'wp_feature_flags_user_flags', true )
			->andReturn( $users );

		$this->assertEquals( $expected, $flag->is_enabled( 1 ) );
		FlagRegister::instance()->unregister_flag( $flag );
	}

	/**
	 * Data provider for `test_is_enabled` method
	 * 
	 * @return array
	 */
	public function is_enabled_provider(): array {
		return array(
			'enforced'  => array(
				new Flag( 'enforced', 'Test', 'Test', null, 'default', true, false ),
				array(),
				array(),
				true,
			),
			'published' => array(
				new Flag( 'published', 'Test', 'Test', null, 'default', false, false ),
				array( 'published' ),
				array(),
				true,
			),
			'user'      => array(
				new Flag( 'user', 'Test', 'Test', null, 'default', false, false ),
				array(),
				array( 'user' ),
				true,
			),
			'none'      => array(
				new Flag( 'none', 'Test', 'Test', null, 'default', false, false ),
				array(),
				array(),
				false,
			),
			'disabled'  => array(
				new Flag( 'disbaled', 'Test', 'Test', null, 'default', false, true ),
				array( 'disbaled' ),
				array(),
				false,
			),
		);
	}

	/**
	 * Test `jsonSerialize` method
	 */
	public function test_jsonSerialize(): void {
		\WP_Mock::userFunction( 'update_option' );
		\WP_Mock::userFunction( 'get_users' )->andReturn( array() );

		$flag = $this->get_flag();
		$this->assertEquals(
			array(
				'key'            => 'test',
				'name'           => 'Test Flag',
				'description'    => 'This is a test flag',
				'created'        => null,
				'group'          => 'Default',
				'force_enabled'  => false,
				'force_disabled' => false,
				'is_published'   => false,
				'users'          => array(),
			),
			$flag->jsonSerialize()
		);
	}

	/**
	 * Test `publish` method
	 */
	public function test_publish(): void {
		$flag = $this->get_flag();
		\WP_Mock::userFunction( 'update_option' )->once()
			->with( Flag::FLAG_PUBLISH_OPTION, array(), true )
			->andReturn( true );
		\WP_Mock::userFunction( 'update_option' )->once()
			->with( Flag::FLAG_PUBLISH_OPTION, array( 0 => $flag->get_key() ) )
			->andReturn( true );

		$this->assertTrue( $flag->publish() );
	}

	/**
	 * Test `unpublish` method
	 */
	public function test_unpublish(): void {
		$flag = $this->get_flag();
		\WP_Mock::userFunction( 'get_option' )->once()
			->with( Flag::FLAG_PUBLISH_OPTION )
			->andReturn( array( $flag->get_key() ) );
		\WP_Mock::userFunction( 'update_option' )->once()
			->with( Flag::FLAG_PUBLISH_OPTION, array() )
			->andReturn( true );

		$this->assertTrue( $flag->unpublish() );
	}

	/**
	 * Test `publish_for_user` method
	 */
	public function test_publish_for_user(): void {
		$flag = $this->get_flag();
		\WP_Mock::userFunction( 'get_user_meta' )->once()
			->with( 1, 'wp_feature_flags_user_flags', true )
			->andReturn( array() );
		\WP_Mock::userFunction( 'update_user_meta' )->once()
			->with( 1, 'wp_feature_flags_user_flags', array( $flag->get_key() ) )
			->andReturn( true );

		\WP_Mock::userFunction( 'did_action' )->with( 'init' )->andReturn( true );
		\WP_Mock::userFunction( 'get_current_user_id' )->andReturn( 1 );

		$this->assertTrue( $flag->publish_for_user() );
	}

	/**
	 * Test `unpublish_for_user` method
	 */
	public function test_unpublish_for_user(): void {
		$flag = $this->get_flag();
		\WP_Mock::userFunction( 'get_user_meta' )->once()
			->with( 1, 'wp_feature_flags_user_flags', true )
			->andReturn( array( $flag->get_key() ) );
		\WP_Mock::userFunction( 'update_user_meta' )->once()
			->with( 1, 'wp_feature_flags_user_flags', array() )
			->andReturn( true );

		$this->assertTrue( $flag->unpublish_for_user( 1 ) );
	}

	/**
	 * Test for `get_current_user_id()` protected method
	 * 
	 * @param bool $with_init With init.
	 * 
	 * @return void
	 * 
	 * @dataProvider get_current_user_id_provider
	 */
	public function test_get_current_user_id( bool $with_init = false ): void {
		$flag = $this->get_flag();
		\WP_Mock::userFunction( 'get_current_user_id' )->once()->andReturn( 1 );
		\WP_Mock::userFunction( 'did_action' )->once()->with( 'init' )->andReturn( $with_init );
		if ( ! $with_init ) {
			\WP_Mock::userFunction( '_doing_it_wrong' )->once();
		}

		$this->assertEquals( 1, $flag->get_current_user_id() );
	}

	/**
	 * Data provider for `test_get_current_user_id` method
	 * 
	 * @return array
	 */
	public function get_current_user_id_provider(): array {
		return array(
			array( false ),
			array( true ),
		);
	}
}
