<?php
/**
 * Flag register class.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags\FlagRegister;

use BoxUk\WpFeatureFlags\FeatureInterface;
use BoxUk\WpFeatureFlags\DatabaseManager\DatabaseManager;
use BoxUk\WpFeatureFlags\Flag\Flag;

final class FlagRegister implements FeatureInterface {
	private const FEATURE_LABEL = 'flag_register';

	/**
	 * If a flag is not explicitly in a group, we use the 'All' group.
	 */
	private const DEFAULT_GROUP = 'All';

	/**
	 * DatabaseManager instance
	 *
	 * @var DatabaseManager
	 */
	private $database_manager;

	/**
	 * Array of registered flags.
	 *
	 * @var array
	 */
	public $flag_register = [];

	/**
	 * Array of published flags.
	 *
	 * @var array
	 */
	public $published_flags = [];

	/**
	 * Array of preview flags.
	 *
	 * @var array
	 */
	public $preview_flags = [];

	/**
	 * Label for this feature.
	 *
	 * @return string
	 */
	public static function get_label(): string {
		return self::FEATURE_LABEL;
	}

	/**
	 * Whether the feature is enabled. This could come from a setting value.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return true;
	}

	/**
	 * Init method to initialise the feature.
	 *
	 * @return void
	 */
	public function init(): void {
		// Set up the flag register.
		$this->flag_register = apply_filters( WP_FEATURE_FLAGS_PREFIX_SNAKE . '_register_flags', $this->flag_register );
		add_filter( WP_FEATURE_FLAGS_PREFIX_SNAKE . '_register_flags', [ $this, 'set_all_flag_defaults' ], 99, 1 );

		// Set up currently published flags.
		$this->database_manager = new DatabaseManager();
		$this->published_flags = $this->database_manager->get_published_flags();

		// Set up user preview flags.
		add_action( 'init', [ $this, 'setup_preview_flags' ] );
		add_action( WP_FEATURE_FLAGS_PREFIX_SNAKE . '_before_update_previews', [ $this, 'setup_preview_flags' ] );

		// Create AJAX actions for toggling flags.
		add_action( 'wp_ajax_toggle_feature', [ $this, 'ajax_toggle_feature' ] );
		add_action( 'wp_ajax_toggle_preview', [ $this, 'ajax_toggle_preview' ] );
	}

	/**
	 * Converts the register array into an array of Flag class elements.
	 *
	 * @param array $flag_register Original flag array.
	 * @return array Converted array of Flag class elements.
	 */
	public function convert_register_to_flag_class( array $flag_register ): array {
		$converted_arr = [];

		foreach ( $flag_register as $flag_key => $flag_arr ) {
			$converted_arr[ $flag_key ] = $this->get_flag_by_key( $flag_key );
		}

		return $converted_arr;
	}

	/**
	 * Returns all the registered flags.
	 *
	 * @return array
	 */
	public function get_all_registered_flags(): array {
		return $this->flag_register;
	}

	/**
	 * Returns an alphabetically sorted array of unique groups.
	 * Default group name is always at the top.
	 *
	 * @param array $flag_arr Array of flags.
	 * @return array Array of unique group names.
	 */
	public function get_unique_groups( array $flag_arr ): array {
		$all_labels = [
			self::DEFAULT_GROUP,
		];

		foreach ( $flag_arr as $flag_key => $flag_arr ) {
			$flag_obj = $this->get_flag_by_key( $flag_key );
			$all_labels[] = $flag_obj->get_group();
		}

		$unique_labels = array_unique( $all_labels );
		asort( $unique_labels );
		return $unique_labels;
	}

	/**
	 * Returns all the published flags from the database.
	 *
	 * @return array
	 */
	public function get_published_flags(): array {
		return $this->published_flags;
	}

	/**
	 * Returns all the flags in preview mode from the user's meta data.
	 *
	 * @return array
	 */
	public function get_preview_flags(): array {
		// Required to get the user's meta data.
		$this->setup_preview_flags();

		return $this->preview_flags;
	}

	/**
	 * Return a flag object given its key.
	 *
	 * @param string $flag_key Flag unique identifying key.
	 * @return Flag
	 */
	public function get_flag_by_key( string $flag_key ): Flag {
		// Look up the flag in the registry.
		$registered_flag = $this->set_flag_defaults( $this->flag_register[ $flag_key ] );

		return new Flag(
			$flag_key,
			$registered_flag['name'],
			$registered_flag['description'],
			$registered_flag['meta'],
			$registered_flag['group'],
			$registered_flag['enforced'],
			$registered_flag['stable']
		);
	}

	/**
	 * Returns an array of flag keys within a specific group.
	 *
	 * @param array  $flag_keys Flag unique identifying key.
	 * @param string $group_name Name of the group of flags.
	 * @return array
	 */
	public function get_flags_by_group( array $flag_keys, string $group_name ): array {
		$flags = [];

		foreach ( $flag_keys as $flag_key => $flag_arr ) {
			$flag_obj = $this->get_flag_by_key( $flag_key );

			if ( $flag_obj->get_group() === $group_name ) {
				$flags[ $flag_key ] = $flag_arr;
			}
		}

		return $flags;
	}

	/**
	 * Set default values for all required flag keys.
	 *
	 * @param array $all_flags Raw array of registered flags.
	 * @return array Full flag register with defaults set.
	 */
	public function set_all_flag_defaults( array $all_flags ): array {
		$altered_flags = [];

		foreach ( $all_flags as $flag_key => $flag_arr ) {
			$altered_flags[ $flag_key ] = $this->set_flag_defaults( $flag_arr );
		}

		return $altered_flags;
	}

	/**
	 * Set default values for a registered flag.
	 *
	 * @param array $flag_arr Registered flag.
	 * @return array Registered flag including optional keys with default values.
	 */
	public function set_flag_defaults( array $flag_arr ): array {
		$flag_arr['meta'] = $flag_arr['meta'] ?? [];
		$flag_arr['group'] = $flag_arr['group'] ?? self::DEFAULT_GROUP;
		$flag_arr['enforced'] = $flag_arr['enforced'] ?? false;
		$flag_arr['stable'] = $flag_arr['stable'] ?? false;
		$flag_arr['parent'] = $flag_arr['parent'] ?? '';

		return $flag_arr;
	}

	/**
	 * Function for adding a flag to the register without using apply_filters.
	 *
	 * @param string  $flag_key Unique identifier for the flag.
	 * @param string  $flag_name Name of the flag.
	 * @param string  $flag_description Description of the flag.
	 * @param array   $flag_meta Array of meta key and value pairs for the flag.
	 * @param string  $flag_group Group the flag belongs to.
	 * @param boolean $is_enforced Whether or not the flag is enforced.
	 * @param boolean $is_stable Whether or not hte flag is stable.
	 * @param string  $parent_flag Parent of this flag, if one exists.
	 * @return void
	 */
	public function set_registered_flag(
		string $flag_key,
		string $flag_name,
		string $flag_description,
		array $flag_meta = [],
		string $flag_group = self::DEFAULT_GROUP,
		bool $is_enforced = false,
		bool $is_stable = false,
		string $parent_flag = ''
	): void {
		$flag_arr = [
			'key' => $flag_key,
			'name' => $flag_name,
			'description' => $flag_description,
			'meta' => $flag_meta,
			'group' => $flag_group,
			'enforced' => $is_enforced,
			'stable' => $is_stable,
			'parent' => $parent_flag,
		];

		$flag_arr = $this->set_flag_defaults( $flag_arr );

		$this->flag_register[ $flag_key ] = $flag_arr;
	}

	/**
	 * Sets the database option for published flags.
	 *
	 * @param array $published_flags Array of published flags.
	 * @return void
	 */
	public function set_published_flags( array $published_flags ): void {
		$this->published_flags = $this->database_manager->set_published_flags( $published_flags );
	}

	/**
	 * Enable all flags in the array passed to the function.
	 *
	 * @param array   $flag_keys Array of flag keys to change publish status of.
	 * @param boolean $flag_published Whether the flags should be published or unpublished.
	 * @return void
	 */
	public function set_flag_publish_status_batch( array $flag_keys = [], bool $flag_published = false ): void {
		foreach ( $flag_keys as $flag_key ) {
			$this->set_flag_publish_status( $flag_key, $flag_published );
		}
	}

	/**
	 * Enable individual flags by their keys.
	 *
	 * @param string  $flag_key The unique identifier key of the flag.
	 * @param boolean $flag_published Whether to publish or unpublish.
	 * @return void
	 */
	public function set_flag_publish_status( string $flag_key, bool $flag_published = false ): void {
		$flag = $this->get_flag_by_key( $flag_key );

		if ( $flag->is_published() === $flag_published ) {
			return;
		}

		// If flag is enforced or unstable, we cannot change its status here.
		if ( false === $flag->can_be_published() ) {
			return;
		}

		$this->published_flags = $this->update_published_flags( $flag_key, $flag_published, $this->published_flags );
		$this->database_manager->set_published_flags( $this->published_flags );
	}

	/**
	 * Update the published flags array.
	 *
	 * @param string  $flag_key The unique identifier key of the flag.
	 * @param boolean $flag_published Whether to publish or unpublish.
	 * @param array   $current_flags Array of published flags.
	 * @return array Updated array of published flags.
	 */
	public function update_published_flags( string $flag_key, bool $flag_published, array $current_flags ): array {
		$updated_flags = $current_flags;
		$current_flag = $this->get_flag_by_key( $flag_key );

		if ( true === $flag_published ) {
			// If we want to publish the flag, check it's not already published.
			if ( false === $current_flag->is_published() && true === $current_flag->can_be_published() ) {
				$updated_flags[] = $flag_key;
			}
		} else {
			// If we want to unpublish the flag, check it's not already unpublished.
			if ( true === $current_flag->is_published() && true === $current_flag->can_be_published() ) {
				$updated_flags = array_diff( $updated_flags, [ $flag_key ] );
			}
		}

		return array_unique( $updated_flags );
	}

	/**
	 * Change the preview status of a given flag.
	 *
	 * @param string  $flag_key The unique ID of the flag.
	 * @param boolean $flag_preview Whether to turn preview on or off.
	 * @param integer $user_id The unique ID of the user.
	 * @return void
	 */
	public function set_flag_preview_status( string $flag_key, bool $flag_preview = false, int $user_id = 0 ): void {
		$flag = $this->get_flag_by_key( $flag_key );

		// Only logged in users can preview flags.
		if ( 0 === $user_id ) {
			return;
		}

		// Can't preview a published flag.
		if ( $flag->is_published() === true ) {
			return;
		}

		// If flag is enforced or unstable, we cannot change its status here.
		if ( false === $flag->can_be_published() ) {
			return;
		}

		$this->preview_flags = $this->update_preview_flags( $flag_key, $flag_preview, $this->preview_flags, $user_id );
		$this->database_manager->set_preview_flags( $this->preview_flags, $user_id );
	}

	/**
	 * Updates the array of flags currently in preview mode for a user.
	 *
	 * @param string  $flag_key The unique identifier key of the flag.
	 * @param boolean $flag_preview Whether or not to switch preview mode on or off.
	 * @param array   $preview_flags The arrray of currently previewed flags.
	 * @param integer $user_id ID of the user.
	 * @return array
	 */
	public function update_preview_flags( string $flag_key, bool $flag_preview, array $preview_flags, int $user_id = 0 ): array {
		// Action to make sure preview flags have been set up.
		do_action( WP_FEATURE_FLAGS_PREFIX_SNAKE . '_before_update_previews' );

		$updated_flags = $preview_flags;
		$current_flag = $this->get_flag_by_key( $flag_key );

		if ( true === $flag_preview ) {
			// If we want to preview the flag, check it's not already previewed.
			if ( false === $current_flag->in_preview_state() ) {
				$updated_flags[] = $flag_key;
			}
		} else {
			// If we want to stop previewing the flag, check it's not already not previewed.
			if ( true === $current_flag->in_preview_state() ) {
				$updated_flags = array_diff( $updated_flags, [ $flag_key ] );
			}
		}

		return $updated_flags;
	}

	/**
	 * Wrapper for toggling flags.
	 * Action is fired when publishing a flag via AJAX.
	 *
	 * @return void
	 */
	public function ajax_toggle_feature(): void {
		$this->ajax_toggle( 'toggle_feature' );
	}

	/**
	 * Wrapper for previewing flags.
	 * Action is fired when publishing a flag via AJAX.
	 *
	 * @return void
	 */
	public function ajax_toggle_preview(): void {
		$this->ajax_toggle( 'toggle_preview' );
	}

	/**
	 * Toggles either feature flags or flag previews.
	 *
	 * @param string $toggle_action Name of the WP action invoked.
	 * @return void
	 */
	public function ajax_toggle( string $toggle_action ): void {
		if (
			//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			isset( $_POST['flag_key'] ) && isset( $_POST['flag_direction'] ) &&
			check_ajax_referer( 'wp_feature_flags_admin_secret', 'secret' )
		) {
			$flag_key = sanitize_text_field( wp_unslash( $_POST['flag_key'] ) );
			$flag_direction = sanitize_text_field( wp_unslash( $_POST['flag_direction'] ) );
			$flag_direction_bool = ( 'on' === $flag_direction ) ? true : false;

			if ( 'toggle_preview' === $toggle_action ) {
				$this->set_flag_preview_status( $flag_key, $flag_direction_bool, get_current_user_id() );
			} elseif ( 'toggle_feature' === $toggle_action ) {
				$this->set_flag_publish_status( $flag_key, $flag_direction_bool );
			}

			wp_send_json_success(
				[
					'action' => $toggle_action,
					'flag' => $flag_key,
					'direction' => $flag_direction,
				]
			);
		}
	}

	/**
	 * Set up the preview flags.
	 * Needs to be run here to be sure 'get_current_user_id' is available.
	 *
	 * @return void
	 */
	public function setup_preview_flags(): void {
		// Set up user preview flags.
		if ( get_current_user_id() > 0 ) {
			$this->preview_flags = $this->database_manager->get_preview_flags( get_current_user_id() );
		}
	}

	/**
	 * Uninstall method to remove any data created by the feature.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		( new FlagRegisterUninstaller() )->uninstall();
	}
}
