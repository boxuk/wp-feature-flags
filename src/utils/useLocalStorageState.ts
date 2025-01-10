import { useState } from 'react';

export const useLocalStorageState = < T >(
	key: string,
	defaultValue: T,
	typeChecker: ( value: unknown ) => value is T = ( value ): value is T =>
		typeof value === 'object' && value !== null
): [ T, ( value: T ) => void ] => {
	const [ state, setState ] = useState< T >( () => {
		const storedValue = localStorage.getItem( key );
		if ( ! storedValue ) {
			return defaultValue;
		}

		const parsedValue = JSON.parse( storedValue ) as unknown;
		if ( ! typeChecker( parsedValue ) ) {
			return defaultValue;
		}

		return parsedValue;
	} );

	const setStoredState = ( value: T ) => {
		setState( value );
		localStorage.setItem( key, JSON.stringify( value ) );
	};

	return [ state, setStoredState ];
};
