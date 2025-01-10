<?php
/**
 * Flag class.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags;

use DateTime;
use JsonSerializable;

/**
 * Class Flag
 */
class Flag implements JsonSerializable {

	private const DEFAULT_GROUP      = 'Default';
	public const FLAG_PUBLISH_OPTION = 'wp_feature_flags_published_flags';
	public const FLAG_USER_OPTION    = 'wp_feature_flags_user_flags';

	/**
	 * Constructor for the class.
	 *
	 * @param string   $key Unique identifier for the flag.
	 * @param string   $name Human readable name for the flag.
	 * @param string   $description Description of the flag.
	 * @param DateTime $created Array of meta keys and values related to the flag.
	 * @param string   $group Group the flag belongs to.
	 * @param boolean  $force_enabled Whether the flag is forcibly enabled or not.
	 * @param boolean  $force_disabled Whether the flag is forcibly disabled or not.
	 */
	public function __construct(
		private string $key,
		private string $name,
		private string $description,
		private ?DateTime $created = null,
		private string $group = self::DEFAULT_GROUP,
		private bool $force_enabled = false,
		private bool $force_disabled = false,
	) {}

	/**
	 * Return a flag object given
	 *
	 * @return string
	 */
	public function get_key(): string {
		return $this->key;
	}

	/**
	 * Return the name of the flag.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Return the description of the flag.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Return the meta array of the flag.
	 *
	 * @return ?DateTime
	 */
	public function get_created(): ?DateTime {
		return $this->created;
	}

	/**
	 * Return the group the flag belongs to.
	 *
	 * @return string
	 */
	public function get_group(): string {
		return $this->group;
	}

	/**
	 * Is the flag enforced or not?
	 *
	 * @return boolean
	 */
	public function is_force_enabled(): bool {
		return $this->force_enabled;
	}

	/**
	 * Is the flag forcibly disabled or not?
	 *
	 * @return boolean
	 */
	public function is_force_disabled(): bool {
		return $this->force_disabled;
	}

	/**
	 * Whether the flag can be published or not.
	 *
	 * @return boolean
	 */
	public function can_be_published(): bool {
		if ( true === $this->force_disabled ) {
			return false;
		}

		// Enforced flags cannot be published.
		if ( true === $this->force_enabled ) {
			return false;
		}

		return true;
	}

	/**
	 * Is Published
	 * 
	 * @return bool
	 */
	public function is_published(): bool {
		return in_array( $this, $this->get_published_flags(), true );
	}

	/**
	 * Users with flag
	 * 
	 * @return array<int>
	 */
	public function get_users(): array {
		$users = array();
		foreach ( get_users( array( 'fields' => 'ID' ) ) as $user_id ) {
			$user_flags = $this->get_user_flags( absint( $user_id ) );
			if ( in_array( $this, $user_flags, true ) ) {
				$users[] = absint( $user_id );
			}
		}
		return $users;
	}

	/**
	 * Is on for a user
	 * 
	 * @param int $user_id The user ID to check if the flag is on for.
	 * 
	 * @return bool
	 */
	public function is_enabled_for_user( int $user_id = 0 ): bool {
		return in_array( $this, $this->get_user_flags( $user_id ), true );
	}

	/**
	 * Is On
	 * 
	 * @param int $user_id The user ID to check if the flag is on for.
	 */
	public function is_enabled( int $user_id = 0 ): bool {
		if ( $this->is_force_enabled() ) {
			return true;
		}

		if ( $this->is_force_disabled() ) {
			return false;
		}
		
		if ( $this->is_published() ) {
			return true;
		}

		if ( $this->is_enabled_for_user( $user_id ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Json serializable method.
	 * 
	 * @return array{key:string,name:string,description:string,created:DateTime|null,group:string,force_enabled:bool,force_disabled:bool,is_published:bool,users:array<int>}
	 */
	public function jsonSerialize(): array {
		return array(
			'key'            => $this->key,
			'name'           => $this->name,
			'description'    => $this->description,
			'created'        => $this->created,
			'group'          => $this->group,
			'force_enabled'  => $this->force_enabled,
			'force_disabled' => $this->force_disabled,
			'is_published'   => $this->is_published(),
			'users'          => $this->get_users(),
		);
	}

	/**
	 * Publish a flag.
	 */
	public function publish(): bool {
		$published_flags   = $this->get_published_flags();
		$published_flags[] = $this;
		return $this->set_published_flags( $published_flags );
	}

	/**
	 * Unpublish a flag.
	 */
	public function unpublish(): bool {
		$published_flags = $this->get_published_flags();
		$published_flags = array_filter(
			$published_flags,
			fn( $published_flag ) => $published_flag->get_key() !== $this->get_key()
		);
		return $this->set_published_flags( $published_flags );
	}

	/**
	 * Add a flag to the user's list.
	 * 
	 * @param int $user_id The user ID to add the flag to.
	 */
	public function publish_for_user( int $user_id = 0 ): bool {
		$user_flags   = $this->get_user_flags( $user_id );
		$user_flags[] = $this;
		return $this->set_user_flags( $user_flags, $user_id );
	}

	/**
	 * Remove a flag from the user's list.
	 * 
	 * @param int $user_id The user ID to remove the flag from.
	 */
	public function unpublish_for_user( int $user_id = 0 ): bool {
		$user_flags = $this->get_user_flags( $user_id );
		$user_flags = array_filter(
			$user_flags,
			fn( $flag ) => $flag->get_key() !== $this->get_key()
		);
		return $this->set_user_flags( $user_flags, $user_id );
	}

	/**
	 * Get the currently published flags.
	 *
	 * @return array<Flag>
	 */
	protected function get_published_flags(): array {
		$option_key      = self::FLAG_PUBLISH_OPTION;
		$published_flags = get_option( $option_key );

		if ( ! is_array( $published_flags ) ) {
			update_option( $option_key, array(), true );
			return array();
		}

		return Sanitizer::sanitize_flags( $published_flags );
	}

	/**
	 * Get the flags that are currently enabled for the given user.
	 *
	 * @param integer $user_id The current user ID.
	 * @return array<Flag>
	 */
	protected function get_user_flags( int $user_id = 0 ): array {

		if ( 0 === $user_id ) {
			$user_id = $this->get_current_user_id();
		}

		$meta_key   = self::FLAG_USER_OPTION;
		$user_flags = get_user_meta( $user_id, $meta_key, true );

		if ( ! is_array( $user_flags ) ) {
			add_user_meta( $user_id, $meta_key, array(), true );
			return array();
		}

		return Sanitizer::sanitize_flags( $user_flags );
	}

	/**
	 * Set the published flags.
	 *
	 * @param array<string|Flag> $flags The published flags.
	 * @return bool
	 */
	protected function set_published_flags( array $flags ): bool {
		$published_option = self::FLAG_PUBLISH_OPTION;
		return update_option( 
			$published_option, 
			array_unique(
				array_map( 
					fn( Flag $flag ) => $flag->get_key(),
					Sanitizer::sanitize_flags( $flags ) 
				)
			)
		);
	}

	/**
	 * Sets users enabled flags.
	 *
	 * @param array<string|Flag> $flags    The flags to enable for the user.
	 * @param integer            $user_id  The current user ID.
	 * 
	 * @return bool True on successful update, false on failure or if the value passed to the function is the same as the one that is already in the database.
	 */
	protected function set_user_flags( array $flags, int $user_id = 0 ): bool {

		if ( 0 === $user_id ) {
			$user_id = $this->get_current_user_id();
		}

		return (bool) update_user_meta( 
			$user_id, 
			self::FLAG_USER_OPTION, 
			array_map( 
				fn( Flag $flag ) => $flag->get_key(),
				Sanitizer::sanitize_flags( $flags ) 
			)
		);
	}

	/**
	 * Get Current User ID
	 * 
	 * @return int
	 */
	public function get_current_user_id(): int {
		if ( ! did_action( 'init' ) ) {
			_doing_it_wrong( __METHOD__, 'This method should only be called after the init hook. We cannot retrieve the current user ID.', '1.0.0' );
		}
		return get_current_user_id();
	}
}
