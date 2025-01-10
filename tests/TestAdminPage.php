<?php
/**
 * Test AdminPage class
 *
 * @package BoxUk\WpFeatureFlags
 */

declare( strict_types = 1 );

namespace BoxUk\WpFeatureFlags;

use WP_Mock\Tools\TestCase;

/**
 * AdminPage class
 */
class TestAdminPage extends TestCase {

	/**
	 * Test `init` method
	 */
	public function test_init() {
		$admin_page = new AdminPage();
		\WP_Mock::expectActionAdded( 'admin_menu', array( $admin_page, 'add_settings_menu_page' ) );
		\WP_Mock::expectActionAdded( 'admin_enqueue_scripts', array( $admin_page, 'enqueue_scripts' ) );
		$admin_page->init();
		$this->assertConditionsMet();
	}

	/**
	 * Test `add_settings_menu_page` method
	 */
	public function test_add_settings_menu_page() {
		$admin_page = new AdminPage();

		\WP_Mock::userFunction(
			'add_submenu_page',
			array(
				'args' => array(
					'tools.php',
					__( 'Feature flags', 'boxuk' ),
					__( 'Feature flags', 'boxuk' ),
					'manage_options',
					'wp-feature-flags',
					array( $admin_page, 'add_menu_page_content' ),
				),
			)
		);
		
		$admin_page->add_settings_menu_page();

		$this->assertConditionsMet();
	}

	/**
	 * Test `add_menu_page_content` method
	 */
	public function test_add_menu_page_content() {
		$admin_page = new AdminPage();

		$admin_page->add_menu_page_content();
		$this->assertEqualsHtml( '<div id="boxuk-wp-feature-flags"></div>', $this->getActualOutputForAssertion() );
	}

	/**
	 * Test `enqueue_scripts` method
	 * 
	 * @dataProvider data_enqueue_scripts
	 * 
	 * @param string $screen_name  Screen name.
	 * @param bool   $should_enqueue Should enqueue.
	 * 
	 * @return void
	 */
	public function test_enqueue_scripts( string $screen_name, bool $should_enqueue ) {
		$admin_page = $this->createPartialMock(
			AdminPage::class,
			array(
				'get_asset_path',
			)
		);

		$admin_page->method( 'get_asset_path' )->willReturn( __DIR__ . '/fixtures/index.asset.php' );

		\WP_Mock::userFunction(
			'get_current_screen',
			array(
				'return' => (object) array( 'id' => $screen_name ),
			) 
		);

		if ( ! $should_enqueue ) {
			$admin_page->enqueue_scripts();
			$this->assertConditionsMet();
			return;
		}

		\WP_Mock::userFunction( 'plugins_url' )->andReturn( 'http://example.com' );

		\WP_Mock::userFunction( 'wp_enqueue_script' )->once()->with(
			'wp-feature-flags-admin',
			'http://example.com',
			array( 'test' ),
			'test',
			true
		);

		\WP_Mock::userFunction( 'wp_enqueue_style' )->once()->with(
			'wp-feature-flags-admin',
			'http://example.com',
			array(),
			'test'
		);

		\WP_Mock::userFunction( 'wp_enqueue_style' )->once()->with(
			'wp-feature-flags-admin-style',
			'http://example.com',
			array(),
			'test'
		);

		\WP_Mock::userFunction( 'wp_enqueue_style' )->once()->with( 'wp-edit-post' );
		\WP_Mock::expectAction( 'enqueue_block_editor_assets' );

		$admin_page->enqueue_scripts();
		$this->assertConditionsMet();
	}

	/**
	 * Data provider for `test_enqueue_scripts` method
	 * 
	 * @return array
	 */
	public function data_enqueue_scripts(): array {
		return array(
			array( 'tools_page_wp-feature-flags', true ),
			array( 'tools_page_wp-feature-flags2', false ),
			array( 'tools_page_wp-feature-flags3', false ),
		);
	}

	/**
	 * Test `get_asset_path` method
	 */
	public function test_get_asset_path() {
		$admin_page = new AdminPage();
		$this->assertStringContainsString( 'index.asset.php', $admin_page->get_asset_path( 'index.asset.php' ) );
	}
}
