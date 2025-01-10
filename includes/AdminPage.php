<?php
/**
 * Plugin Settings.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags;

/**
 * AdminPage class.
 */
class AdminPage {

	protected const PREFIX = 'wp-feature-flags';

	/**
	 * AdminPage Initialization.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'add_settings_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Create plugin menu page.
	 *
	 * @return void
	 */
	public function add_settings_menu_page(): void {
		add_submenu_page(
			'tools.php',
			__( 'Feature flags', 'boxuk' ),
			__( 'Feature flags', 'boxuk' ),
			'manage_options',
			self::PREFIX,
			array( $this, 'add_menu_page_content' ),
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function add_menu_page_content(): void {
		echo '<div id="boxuk-wp-feature-flags"></div>';
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {

		$screen = get_current_screen();
		if ( ! $screen || 'tools_page_' . self::PREFIX !== $screen->id ) {
			return;
		}

		$asset = require $this->get_asset_path( 'index.asset.php' ); // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable

		wp_enqueue_script(
			self::PREFIX . '-admin',
			$this->get_asset_url( 'index.js' ),
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_enqueue_style(
			self::PREFIX . '-admin',
			$this->get_asset_url( 'index.css' ),
			array(),
			$asset['version']
		);

		wp_enqueue_style(
			self::PREFIX . '-admin-style',
			$this->get_asset_url( 'style-index.css' ),
			array(),
			$asset['version']
		);

		wp_enqueue_style( 'wp-edit-post' );
		do_action( 'enqueue_block_editor_assets' );
	}

	/**
	 * Get Asset URL
	 * 
	 * @param string $filename The filename.
	 * 
	 * @return string
	 */
	public function get_asset_url( string $filename ): string {
		return plugins_url( '/build/' . $filename, __DIR__ );
	}

	/**
	 * Get Asset Path
	 * 
	 * @param string $filename The filename.
	 * 
	 * @return string
	 */
	public function get_asset_path( string $filename ): string {
		return dirname( __DIR__ ) . '/build/' . $filename;
	}
}
