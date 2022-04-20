<?php
/**
 * Plugin Settings.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags\Admin;

use BoxUk\WpFeatureFlags\Flag\Flag;
use BoxUk\WpFeatureFlags\FlagRegister\FlagRegister;

class PluginSettings {
	private const MENU_POSITION = 80;

	/**
	 * Flag register class.
	 *
	 * @var FlagRegister
	 */
	public $flag_register;

	/**
	 * PluginSettings Initialization.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'admin_menu', [ __CLASS__, 'add_settings_menu_page' ] );
		add_action( 'admin_notices', [ __CLASS__, 'display_notices' ] );
	}

	/**
	 * Create plugin menu page.
	 *
	 * @return void
	 */
	public static function add_settings_menu_page(): void {
		add_menu_page(
			__( 'Feature flags', 'wp-feature-flags' ),
			__( 'Feature flags', 'wp-feature-flags' ),
			'manage_options',
			WP_FEATURE_FLAGS_PREFIX . '-settings',
			[ __CLASS__, 'add_menu_page_content' ],
			'dashicons-flag',
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
		echo esc_html__( 'WP Feature flags', 'wp-feature-flags' );
		echo '</h1>';
		echo '</div>';
		echo '<hr class="wp-header-end" />';
		echo '<form method="post" action="options.php">';

		// Flags by group.
		self::display_available_flags();

		// Enforced flags.
		self::display_enforced_flags();

		echo '</div>';
	}

	/**
	 * Renders the 'Available' flags section.
	 *
	 * @todo   get all available flags from register, loop through flags by label.
	 * @return void
	 */
	public static function display_available_flags(): void {
		echo '<h2>' . esc_html__( 'Available flags', 'wp-feature-flags' ) . '</h2>';
		echo '<p>' . esc_html__( 'The following flags can be used to publish or unpublish features to this site.', 'wp-feature-flags' ) . '</p>';
		echo '<p>' . esc_html__( 'Flags that have been marked as \'unstable\' will not be publishable.', 'wp-feature-flags' ) . '</p>';

		$flag_register = new FlagRegister();
		$flag_register->init();

		// Get all available flags and groups.
		$registered_flags = $flag_register->get_all_registered_flags();
		$flag_groups = $flag_register->get_unique_groups( $registered_flags );

		if ( count( $flag_groups ) > 1 ) {
			// For each flag group.
			foreach ( $flag_groups as $flag_group ) {
				// Filter flags by group.
				$group_flags = $flag_register->get_flags_by_group(
					$registered_flags,
					$flag_group
				);

				// Display the group flag table.
				self::display_flag_table( $group_flags, $flag_group );
			}
		} else {
			self::display_flag_table( $registered_flags );
		}
	}

	/**
	 * Renders the 'Enforced' flags section.
	 *
	 * @todo   get enforced flags from the register.
	 * @return void
	 */
	public static function display_enforced_flags(): void {
		echo '<h2>' . esc_html__( 'Enforced flags', 'wp-feature-flags' ) . '</h2>';
		echo '<p>' . esc_html__( 'The following flags are published via code. This is usually because they have been safe enough to use on this environment.', 'wp-feature-flags' ) . '</p>';

		// Get enforced flags.
		self::display_flag_table( [] );
	}

	/**
	 * Renders meta data associated with the flag.
	 * Follows the style of the WordPress admin page for enabling plugins.
	 *
	 * @param  array $flag_meta_arr Array of flags.
	 * @return void
	 */
	public static function display_flag_metadata( array $flag_meta_arr ): void {
		echo '<div><p>';
		foreach ( $flag_meta_arr as $flag_meta ) {
			if ( false === array_key_exists( 'label', $flag_meta ) || false === array_key_exists( 'value', $flag_meta ) ) {
				continue;
			}

			echo '<strong>' . esc_html( $flag_meta['label'] ) . ':</strong> ';

			if ( true === array_key_exists( 'link', $flag_meta ) ) {
				echo '<a href="' . esc_url( $flag_meta['link'] ) . '" target="_blank" title="' . esc_attr( $flag_meta['value'] ) . '">';
			}

			echo esc_html( $flag_meta['value'] );

			if ( true === array_key_exists( 'link', $flag_meta ) ) {
				echo '</a>';
			}

			if ( end( $flag_meta_arr ) !== $flag_meta ) {
				echo ' | ';
			}
		}
		echo '</p></div>';
	}

	/**
	 * Converts a list of feature flags into a table.
	 *
	 * @param array  $flag_list List of flags.
	 * @param string $group_title Title of the flag group, if one exists.
	 * @return void
	 */
	public static function display_flag_table( array $flag_list, string $group_title = '' ): void {
		if ( 0 === count( $flag_list ) ) {
			return;
		}

		if ( '' !== $group_title ) :
			?>
			<h4><?php echo esc_html( $group_title ); ?></h4>
			<?php
		endif;
		?>

		<table class="widefat wp-feature-flags-table">
			<thead>
				<tr>
					<th class="row-title">
						<?php esc_html_e( 'Feature', 'wp-feature-flags' ); ?>
					</th>
					<th>
						<?php esc_html_e( 'Key', 'wp-feature-flags' ); ?>
					</th>
					<th>
						<?php esc_html_e( 'Description', 'wp-feature-flags' ); ?>
					</th>
					<th>
						<?php esc_html_e( 'Preview', 'wp-feature-flags' ); ?>
					</th>
					<th colspan="2">
						<?php esc_html_e( 'Actions', 'wp-feature-flags' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $flag_list as $flag_key => $flag_detail ) :
					$flag = new Flag(
						$flag_key,
						$flag_detail['name'],
						$flag_detail['description'],
						$flag_detail['meta'],
						$flag_detail['group'],
						$flag_detail['enforced'],
						$flag_detail['stable']
					);

					// Set up preview button variables.
					$preview_button_text = __( 'On', 'wp-feature-flags' );
					$preview_button_class = 'primary small wp-feature-flags__button';
					$preview_button_name = 'wp-feature-flags-preview';
					$preview_button_args = [
						'class' => 'action-btn',
						'data-action' => 'toggle_preview',
						'data-toggle' => 'on',
						'data-flag' => $flag_key,
					];

					if ( $flag->in_preview_state() ) {
						$preview_button_text = __( 'Off', 'wp-feature-flags' );
						$preview_button_args['data-toggle'] = 'off';
					}

					// Set up publish/unpublish button variables.
					$publish_button_text = __( 'Publish', 'wp-feature-flags' );
					$publish_button_class = 'primary small wp-feature-flags__button';
					$publish_button_name = 'wp-feature-flags-publish';
					$publish_button_args = [
						'data-action' => 'toggle_feature',
						'data-toggle' => 'on',
						'data-flag' => $flag_key,
					];

					if ( $flag->is_published() ) {
						$publish_button_text = __( 'Unpublish', 'wp-feature-flags' );
						$publish_button_args['data-toggle'] = 'off';

						// Published flags cannot be previewed.
						$preview_button_args['disabled'] = true;
					}

					if ( false === $flag->is_stable() || $flag->is_enforced() ) {
						$preview_button_args['disabled'] = true;
						$publish_button_args['disabled'] = true;
					}
					?>
					<tr>
						<td class="title">
							<strong>
								<?php echo esc_html( $flag->get_name() ); ?>
							</strong>
							<?php
							if ( false === $flag->is_stable() || $flag->is_enforced() ) :
								if ( $flag->is_enforced() ) {
									$status_message = __( 'Enforced feature', 'wp-feature-flags' );
									$status_class = 'wp-feature-flags__status-label--info';
								} else {
									$status_message = __( 'Unstable feature', 'wp-feature-flags' );
									$status_class = 'wp-feature-flags__status-label--warning';
								}
								?>
								<br />
								<span class="wp-feature-flags__status-label <?php echo esc_attr( $status_class ); ?>">
									<?php echo esc_html( $status_message ); ?>
								</span>
							<?php endif; ?>
						</td>
						<td>
							<code>
								<?php echo esc_html( $flag->get_key() ); ?>
							</code>
						</td>
						<td>
							<?php
							echo '<p>' . esc_html( $flag->get_description() ) . '</p>';

							if ( [] !== $flag->get_meta() ) {
								self::display_flag_metadata( $flag->get_meta() );
							}
							?>

						</td>
						<td>
							<?php
							// Submit button to enter/exit preview mode.
							submit_button(
								$preview_button_text,
								$preview_button_class,
								$preview_button_name,
								false,
								$preview_button_args
							);
							?>
						</td>
						<td>
							<?php
							// Submit button to publish/unpublish.
							submit_button(
								$publish_button_text,
								$publish_button_class,
								$publish_button_name,
								false,
								$publish_button_args
							);
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
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
