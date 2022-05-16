<?php
/**
 * DatabaseManager class.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags\DatabaseManager;

use BoxUk\WpFeatureFlags\FeatureInterface;

final class DatabaseManager implements FeatureInterface {
	private const FEATURE_LABEL = 'database_manager';

	/**
	 * The suffix of the database option for saving .
	 */
	private const FLAG_PUBLISH_OPTION_SUFFIX = '_published_flags';

	/**
	 * The suffix of the database option for saving .
	 */
	private const FLAG_PREVIEW_OPTION_SUFFIX = '_previewing_flags';

	/**
	 * The published flags database record.
	 *
	 * @var array
	 */
	public $published_flags = [];

	/**
	 * The preview flags database record.
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
		$this->published_flags = $this->get_published_flags();

		if ( is_user_logged_in() && get_current_user_id() > 0 ) {
			$this->preview_flags = $this->get_preview_flags( get_current_user_id() );
		}
	}

	/**
	 * Add the database option for published flags if none exists.
	 *
	 * @return void
	 */
	public function add_published_flags_option_if_missing(): void {
		$published_option = WP_FEATURE_FLAGS_PREFIX_SNAKE . self::FLAG_PUBLISH_OPTION_SUFFIX;
		$this->add_database_option_if_missing( $published_option );
	}

	/**
	 * Add the database option for previewing flags if none exists.
	 * Defaults to the current user if none is supplied.
	 *
	 * @param integer $user_id The user ID.
	 * @return void
	 */
	public function add_preview_flags_option_if_missing( int $user_id = 0 ): void {
		$preview_option = WP_FEATURE_FLAGS_PREFIX_SNAKE . self::FLAG_PREVIEW_OPTION_SUFFIX;

		if ( 0 === $user_id && get_current_user_id() > 0 ) {
			$user_id = get_current_user_id();
		}
		$this->add_user_meta_if_missing( $preview_option, $user_id );
	}

	/**
	 * Adds a database option of 'option_key' name if it doesn't exist.
	 *
	 * @param string $option_key The option key.
	 * @return void
	 */
	public function add_database_option_if_missing( string $option_key ): void {
		// Check for an existing array of published flags.
		$existing_key = get_option( $option_key, false );

		if ( false === $existing_key ) {
			// Add our empty value.
			add_option( $option_key, maybe_serialize( [] ) );
		}
	}

	/**
	 * Add meta data of 'meta_key' name if it doesn't exist.
	 *
	 * @param string  $meta_key The key of the meta data.
	 * @param integer $user_id The ID of the current user.
	 * @return void
	 */
	public function add_user_meta_if_missing( string $meta_key, int $user_id ): void {
		// Check for an existing array in meta data.
		$existing_meta = get_user_meta( $user_id, $meta_key, true );

		if ( '' === $existing_meta ) {
			// Add our empty value.
			add_user_meta( $user_id, $meta_key, maybe_serialize( [] ) );
		}
	}

	/**
	 * Get the currently published flags.
	 *
	 * @return array
	 */
	public function get_published_flags(): array {
		$published_option = WP_FEATURE_FLAGS_PREFIX_SNAKE . self::FLAG_PUBLISH_OPTION_SUFFIX;
		$published_flags = maybe_unserialize( get_option( $published_option, false ) );

		if ( false === $published_flags ) {
			$published_flags = [];
			$this->add_published_flags_option_if_missing();
		}

		return $published_flags;
	}

	/**
	 * Get the flags that are currently being previewed for the given user.
	 *
	 * @param integer $user_id The current user ID.
	 * @return array
	 */
	public function get_preview_flags( int $user_id ): array {
		$preview_option = WP_FEATURE_FLAGS_PREFIX_SNAKE . self::FLAG_PREVIEW_OPTION_SUFFIX;
		$preview_flags = maybe_unserialize( get_user_meta( $user_id, $preview_option, true ) );

		if ( '' === $preview_flags ) {
			$preview_flags = [];
			$this->add_preview_flags_option_if_missing( $user_id );
		}

		return $preview_flags;
	}

	/**
	 * Set the published flags.
	 *
	 * @param array $published_flags The published flags.
	 * @return void
	 */
	public function set_published_flags( array $published_flags ): void {
		$published_option = WP_FEATURE_FLAGS_PREFIX_SNAKE . self::FLAG_PUBLISH_OPTION_SUFFIX;
		update_option( $published_option, maybe_serialize( $published_flags ) );
	}

	/**
	 * Sets preview flags.
	 *
	 * @param array   $preview_flags The flags in preview mode.
	 * @param integer $user_id The current user ID.
	 * @return void
	 */
	public function set_preview_flags( array $preview_flags, int $user_id = 0 ): void {
		if ( $user_id > 0 ) {
			update_user_meta( $user_id, WP_FEATURE_FLAGS_PREFIX_SNAKE . self::FLAG_PREVIEW_OPTION_SUFFIX, maybe_serialize( $preview_flags ) );
		}
	}

	/**
	 * Uninstall method to remove any data created by the feature.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		( new DatabaseManagerUninstaller() )->uninstall();
	}
}
