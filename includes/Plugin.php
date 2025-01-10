<?php
/**
 * Main Controller for the plugin.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags;

/**
 * Plugin class.
 */
class Plugin {

	/**
	 * Admin page instance.
	 *
	 * @var AdminPage
	 */
	private AdminPage $admin_page;

	/**
	 * API instance.
	 *
	 * @var Api
	 */
	private Api $api;

	/**
	 * Constructor for the plugin.
	 */
	public function __construct() {
		$this->admin_page = new AdminPage();
		$this->api        = new Api();
	}

	/**
	 * Main run method for the plugin.
	 *
	 * @return void
	 */
	public function run(): void {
		$this->api->init();
		$this->admin_page->init();
	}

	/**
	 * Activate the plugin.
	 * 
	 * @return void
	 */
	public function activate(): void {
		// nothing to do here...
	}

	/**
	 * Deactivate the plugin.
	 * 
	 * @return void
	 */
	public function deactivate(): void {
		delete_option( Flag::FLAG_PUBLISH_OPTION );
		foreach ( get_users( array( 'fields' => 'ID' ) ) as $user_id ) {
			delete_user_meta( $user_id, Flag::FLAG_USER_OPTION );
		}
		wp_cache_flush();
	}
}
