<?php
/**
 * Plugin Settings.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton\Admin;

use BoxUk\WpPluginSkeleton\PluginSettingsManager;

class PluginSettings {
	private const MENU_POSITION = 80;
	private const OPTION_GROUP_NAME = BOXUK_PLUGIN_SKELETON_PREFIX . '_settings';
	private const PAGE = BOXUK_PLUGIN_SKELETON_PREFIX;

	/**
	 * PluginSettings Initialization.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'admin_menu', [ __CLASS__, 'add_settings_menu_page' ] );
		add_action( 'admin_init', [ __CLASS__, 'add_register_setting' ] );
		add_action( 'admin_notices', [ __CLASS__, 'display_notices' ] );
	}

	/**
	 * Create plugin menu page.
	 *
	 * @return void
	 */
	public static function add_settings_menu_page(): void {
		add_menu_page(
			__( 'BoxUK', 'boxuk' ),
			__( 'BoxUK', 'boxuk' ),
			'manage_options',
			BOXUK_PLUGIN_SKELETON_PREFIX . '-settings',
			[ __CLASS__, 'add_menu_page_content' ],
			'dashicons-boxuk-icon',
			self::MENU_POSITION
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public static function add_menu_page_content(): void {
		echo '<div class="wrap">';
		echo '<div class="boxuk-settings-header">';
		echo '<h1 class="wp-heading-inline">';
		echo '<img src="' . esc_url( BOXUK_PLUGIN_SKELETON_PLUGIN_URL . 'assets/img/logo.svg' ) . '" alt="Box UK logo" />';
		echo '</h1>';
		echo '</div>';
		echo '<hr class="wp-header-end" />';
		echo '<form method="post" action="options.php">';

		settings_fields( self::OPTION_GROUP_NAME );
		do_settings_sections( self::PAGE );
		submit_button();

		echo '</form></div>';
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public static function add_register_setting(): void {
		register_setting(
			self::OPTION_GROUP_NAME,
			BOXUK_PLUGIN_SKELETON_PREFIX . '_enable_plugin'
		);

		add_settings_section(
			self::OPTION_GROUP_NAME . '_section_id',
			'',
			'',
			self::PAGE
		);

		// Enable plugin.
		add_settings_field(
			BOXUK_PLUGIN_SKELETON_PREFIX . '_enable_plugin',
			__( 'Enable Plugin', 'boxuk' ),
			[ __CLASS__, 'boxuk_enable_plugin_field_html' ],
			self::PAGE,
			self::OPTION_GROUP_NAME . '_section_id',
			[
				'label_for' => BOXUK_PLUGIN_SKELETON_PREFIX . '_enable_plugin',
				'class' => 'boxuk-class',
			]
		);
	}

	/**
	 * Render "Enable Plugin" field.
	 *
	 * @return void
	 */
	public static function boxuk_enable_plugin_field_html(): void {
		?>
		<input type="checkbox" name="<?php echo esc_attr( BOXUK_PLUGIN_SKELETON_PREFIX . '_enable_plugin' ); ?>" id="<?php echo esc_attr( BOXUK_PLUGIN_SKELETON_PREFIX . '_enable_plugin' ); ?>" value="1" <?php checked( true, PluginSettingsManager::is_enabled(), true ); ?> />
		<?php
	}

	/**
	 * Display notices to ensure both success and error notices are shown.
	 *
	 * @return void
	 */
	public static function display_notices(): void {
		settings_errors();
	}
}
