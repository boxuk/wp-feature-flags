import { useSelect } from '@wordpress/data';
import { store as CoreStore } from '@wordpress/core-data';
import apiFetch from '@wordpress/api-fetch';

import { store } from '.';
import { getCurrentUserId, isFlagEnabled } from '../utils';

import type { Flag } from '../types';

export const useFlags = () =>
	useSelect( ( select ) => select( store ).getFlags(), [] );

export const useFlag = ( flag: string ) =>
	useSelect(
		( select ) =>
			select( store )
				.getFlags()
				.find( ( f ) => f.key === flag ),
		[ flag ]
	);

export const useFlagEnabled = ( key: string ) => {
	const flag = useFlag( key );
	const currentUserId = useSelect(
		( select ) => select( CoreStore ).getCurrentUser()?.id,
		[]
	);
	if ( ! flag ) {
		console.warn( `Flag ${ key } not found` ); // eslint-disable-line no-console -- We want to log this
		return false;
	}
	return isFlagEnabled( flag, currentUserId );
};

export const getFlags = async () => {
	return apiFetch< Flag[] >( { path: 'feature-flags/v1/flags' } );
};

export const getFlag = async ( key: string ) => {
	const flags = await getFlags();
	return flags.find( ( flag ) => flag.key === key );
};

export const getFlagEnabled = async ( key: string ) => {
	const flag = await getFlag( key );
	const currentUserId = await getCurrentUserId();
	if ( ! flag ) {
		console.warn( `Flag ${ key } not found` ); // eslint-disable-line no-console -- We want to log this
		return false;
	}
	return isFlagEnabled( flag, currentUserId );
};
