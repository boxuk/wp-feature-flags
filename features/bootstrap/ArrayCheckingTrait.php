<?php
/**
 * Useful trait for checking arrays within feature files.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

trait ArrayCheckingTrait {
	/**
	 * Check the data has the required keys.
	 *
	 * @param array $required_keys The required keys array.
	 * @param array $data Data of the array.
	 *
	 * @return void
	 * @throws \DomainException If the array does not contain all required keys.
	 */
	public function has_required_keys( array $required_keys, array $data ): void {
		if ( false === $this->check_all_array_keys_exist( $required_keys, $data ) ) {
			throw new \DomainException( 'You did not set all the required fields in the feature file.' );
		}
	}

	/**
	 * Check all the required keys exist in the data.
	 *
	 * @param array $required_keys The required keys array.
	 * @param array $data Data of the array.
	 *
	 * @return bool
	 */
	private function check_all_array_keys_exist( array $required_keys, array $data ): bool {
		return count( $required_keys ) === count( array_intersect_key( array_flip( $required_keys ), $data ) );
	}
}
