<?php
/**
 * Flag class.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags\Flag;

use BoxUk\WpFeatureFlags\FeatureInterface;
use BoxUk\WpFeatureFlags\FlagRegister\FlagRegister;

final class Flag implements FeatureInterface {
	private const FEATURE_LABEL = 'flag';

	/**
	 * If a flag is not explicitly in a group, we use the 'All' group.
	 */
	private const DEFAULT_GROUP = 'All';

	/**
	 * Unique identifying name for the flag.
	 *
	 * @var string
	 */
	public $flag_key;

	/**
	 * Human readable name for the flag.
	 *
	 * @var string
	 */
	public $flag_name;

	/**
	 * Description of the flag.
	 *
	 * @var string
	 */
	public $flag_description;

	/**
	 * Array of meta keys and values related to the flag.
	 *
	 * @var array
	 */
	public $flag_meta;

	/**
	 * Group the flag belongs to.
	 *
	 * @var string
	 */
	public $flag_group;

	/**
	 * Whether the flag is enforced or not.
	 *
	 * @var bool
	 */
	public $is_enforced;

	/**
	 * Whether the flag is stable or not.
	 *
	 * @var bool
	 */
	public $is_stable;

	/**
	 * Key of a parent flag.
	 *
	 * @var string
	 */
	public $parent_flag;

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
	 * Constructor for the class.
	 *
	 * @param string  $flag_key Unique identifier for the flag.
	 * @param string  $flag_name Human readable name for the flag.
	 * @param string  $flag_description Description of the flag.
	 * @param array   $flag_meta Array of meta keys and values related to the flag.
	 * @param string  $flag_group Group the flag belongs to.
	 * @param boolean $is_enforced Whether the flag is enforced or not.
	 * @param boolean $is_stable Whether the flag is stable or not.
	 * @param string  $parent_flag Key of a parent flag, if any.
	 */
	public function __construct(
		string $flag_key,
		string $flag_name,
		string $flag_description,
		array $flag_meta = [],
		string $flag_group = self::DEFAULT_GROUP,
		bool $is_enforced = false,
		bool $is_stable = false,
		string $parent_flag = ''
	) {
		$this->flag_key = $flag_key;
		$this->flag_name = $flag_name;
		$this->flag_description = $flag_description;
		$this->flag_meta = $flag_meta;
		$this->flag_group = $flag_group;
		$this->is_enforced = $is_enforced;
		$this->is_stable = $is_stable;
		$this->parent_flag = $parent_flag;
	}

	/**
	 * Init method to initialise the feature.
	 *
	 * @return void
	 */
	public function init(): void {
		// Do something.
	}

	/**
	 * Return a flag object given
	 *
	 * @return string
	 */
	public function get_key(): string {
		return $this->flag_key;
	}

	/**
	 * Return the name of the flag.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->flag_name;
	}

	/**
	 * Return the description of the flag.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->flag_description;
	}

	/**
	 * Return the meta array of the flag.
	 *
	 * @return array
	 */
	public function get_meta(): array {
		return $this->flag_meta;
	}

	/**
	 * Return the group the flag belongs to.
	 *
	 * @return string
	 */
	public function get_group(): string {
		return $this->flag_group;
	}

	/**
	 * Return the parent of the flag, if any.
	 *
	 * @return string
	 */
	public function get_parent(): string {
		return $this->parent_flag;
	}

	/**
	 * Is the flag enforced or not?
	 *
	 * @return boolean
	 */
	public function is_enforced(): bool {
		return $this->is_enforced;
	}

	/**
	 * Is the flag stable or not?
	 *
	 * @return boolean
	 */
	public function is_stable(): bool {
		return $this->is_stable;
	}

	/**
	 * Whether or not the flag has a a parent.
	 *
	 * @return boolean
	 */
	public function has_parent(): bool {
		if ( '' !== $this->parent_flag ) {
			// TODO: Add some validation here.
			return true;
		}

		return false;
	}

	/**
	 * Whether the flag is 'on' or not.
	 * This incorporates whether the flag is published or in preview mode.
	 * If the flag is 'on', then it is visible to the current user.
	 *
	 * @return bool
	 */
	public function is_on(): bool {
		// Unstable flags are off.
		if ( false === $this->is_stable() ) {
			return false;
		}

		// Enforced flags are on.
		if ( true === $this->is_enforced() ) {
			return true;
		}

		// Published flags are on.
		if ( true === $this->is_published() ) {
			return true;
		}

		// Flags in preview mode are on.
		if ( true === $this->in_preview_state() ) {
			return true;
		}

		return false;
	}

	/**
	 * Whether the flag is in preview mode for the user or not.
	 *
	 * @return boolean
	 */
	public function in_preview_state(): bool {
		// If the flag can't be published, return false.
		if ( false === $this->can_be_published() ) {
			return false;
		}

		// Cannot preview a published flag.
		if ( true === $this->is_published() ) {
			return false;
		}

		$flag_register = FlagRegister::get_instance();

		if ( in_array( $this->flag_key, $flag_register->get_preview_flags(), true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Whether the flag can be published or not.
	 *
	 * @return boolean
	 */
	public function can_be_published(): bool {
		// Unstable flags canont be published.
		if ( false === $this->is_stable ) {
			return false;
		}

		// Enforced flags cannot be published.
		if ( true === $this->is_enforced ) {
			return false;
		}

		return true;
	}

	/**
	 * Whether the flag is published or not.
	 *
	 * @return boolean
	 */
	public function is_published(): bool {
		// Unstable flags are not published.
		if ( false === $this->is_stable ) {
			return false;
		}

		// Enforced flags are always published.
		if ( true === $this->is_enforced ) {
			return true;
		}

		$flag_register = FlagRegister::get_instance();

		// Flags with a published setting are published.
		if ( in_array( $this->flag_key, $flag_register->get_published_flags(), true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Uninstall method to remove any data created by the feature.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		( new FlagUninstaller() )->uninstall();
	}
}
