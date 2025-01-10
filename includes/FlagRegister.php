<?php
/**
 * Flag register class.
 *
 * @package BoxUk\WpFeatureFlags
 */

declare ( strict_types=1 );

namespace BoxUk\WpFeatureFlags;

use BoxUk\WpFeatureFlags\Flag;

/**
 * Flag Register class.
 */
class FlagRegister {

	/**
	 * Singleton instance.
	 * 
	 * @var ?FlagRegister
	 */
	private static ?FlagRegister $instance = null;

	/**
	 * Get instance.
	 * 
	 * @return FlagRegister
	 */
	public static function instance(): FlagRegister {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/** 
	 * Private contstructor.
	 */
	private function __construct() {}

	/**
	 * Array of flags.
	 *
	 * @var array<string,Flag>
	 */
	private array $flags = array();


	/**
	 * Get a flag.
	 * 
	 * @param Flag|string $flag Anything, but ideally a `Flag` instance or a string which is a key in the flags array.
	 * 
	 * @return Flag|null
	 */
	public function get_flag( Flag|string $flag ): ?Flag {
		if ( $flag instanceof Flag ) {
			return $flag;
		}
		
		if ( array_key_exists( $flag, $this->flags ) ) {
			return $this->flags[ $flag ];
		}

		return null;
	}

	/**
	 * Get Flags
	 * 
	 * @return array<string,Flag>
	 */
	public function get_flags(): array {
		return $this->flags;
	}

	/**
	 * Register a flag.
	 * 
	 * @param Flag $flag The flag to register.
	 */
	public function register_flag( Flag $flag ): void {
		$this->flags[ $flag->get_key() ] = $flag;
	}

	/**
	 * Unregister a flag.
	 * 
	 * @param Flag $flag The flag to unregister.
	 */
	public function unregister_flag( Flag $flag ): void {
		unset( $this->flags[ $flag->get_key() ] );
	}

	/**
	 * Register Flags.
	 * 
	 * @param array<Flag> $flags Array of flag objects.
	 * 
	 * @return self
	 */
	public function register_flags( array $flags ): self {
		foreach ( $flags as $flag ) {
			$this->register_flag( $flag );
		}

		return $this;
	}
}
