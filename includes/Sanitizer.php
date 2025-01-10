<?php
/**
 * Sanitizer (Sanitiser!) class.
 * 
 * @package BoxUk\WpFeatureFlags
 */

declare( strict_types=1 );

namespace BoxUk\WpFeatureFlags;

/**
 * Class Sanitizer
 */
class Sanitizer {

	/**
	 * Sanitisers the flags input.
	 *
	 * @param mixed $input Input.
	 * 
	 * @return array<Flag>
	 */
	public static function sanitize_flags( mixed $input ): array { 
		if ( ! is_array( $input ) ) {
			return [];
		}

		return array_filter(
			array_map( 
				function ( mixed $value ) { 
					return self::sanitize_flag( $value );
				},
				$input
			)
		);
	}

	/**
	 * Sanitises the flag input.
	 *
	 * @param mixed $input Input.
	 * 
	 * @return ?Flag
	 */
	public static function sanitize_flag( mixed $input ): ?Flag {
		if ( $input instanceof Flag ) {
			return $input;
		}

		if ( is_string( $input ) ) {
			return FlagRegister::instance()->get_flag( $input );
		}

		return null;
	}
}
