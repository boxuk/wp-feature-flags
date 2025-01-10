import { select } from '@wordpress/data';
import { store as CoreStore } from '@wordpress/core-data';
import { Flag, ApiDate } from '../types';

export const toDate = ( date?: ApiDate | null ): Date | undefined =>
	date && date.date ? new Date( date.date ) : undefined;

const sanitizeString = ( value: unknown ): string | undefined | null => {
	if ( typeof value === 'string' || value === undefined || value === null ) {
		return value;
	}

	return undefined;
};

const sanitizeBoolean = ( value: unknown ): boolean | undefined | null => {
	if ( typeof value === 'boolean' || value === undefined || value === null ) {
		return value;
	}

	return undefined;
};

const sanitizeNumber = ( value: unknown ): number | undefined | null => {
	if ( typeof value === 'number' || value === undefined || value === null ) {
		return value;
	}

	return undefined;
};

export const canModifyPublishState = ( flag: Flag ): boolean =>
	! flag.force_disabled && ! flag.force_enabled;

export const sanitizeFlag = ( data: unknown ): Flag => {
	if ( typeof data !== 'object' || ! data ) {
		throw new Error( 'Invalid flag data' );
	}

	if (
		! ( 'key' in data ) ||
		! ( 'name' in data ) ||
		! ( 'description' in data ) ||
		! ( 'created' in data ) ||
		! ( 'group' in data ) ||
		! ( 'force_enabled' in data ) ||
		! ( 'force_disabled' in data ) ||
		! ( 'is_published' in data ) ||
		! ( 'users' in data )
	) {
		throw new Error( 'Invalid flag data' );
	}

	return {
		key: sanitizeString( data.key ),
		name: sanitizeString( data.name ),
		description: sanitizeString( data.description ),
		created: sanitizeApiDate( data.created ),
		group: sanitizeString( data.group ),
		force_enabled: sanitizeBoolean( data.force_enabled ),
		force_disabled: sanitizeBoolean( data.force_disabled ),
		is_published: sanitizeBoolean( data.is_published ),
		users: sanitizeArray( data.users, sanitizeString ),
	};
};

export const sanitizeArray = < T >(
	data: unknown,
	typeChecker: ( value: unknown ) => T | undefined | null
): T[] | undefined => {
	if ( Array.isArray( data ) ) {
		return ( data as unknown[] )
			.map( typeChecker )
			.filter(
				( value ): value is T => value !== undefined && value !== null
			);
	}

	return undefined;
};

export const sanitizeApiDate = ( data: unknown ): ApiDate | undefined => {
	if (
		typeof data === 'object' &&
		data &&
		'date' in data &&
		'timezone_type' in data &&
		'timezone' in data
	) {
		return {
			date: sanitizeString( data.date ),
			timezone_type: sanitizeNumber( data.timezone_type ),
			timezone: sanitizeString( data.timezone ),
		};
	}

	return undefined;
};

export const isFlagEnabled = ( flag: Flag, currentUserId: number | null ) => {
	if ( flag.force_enabled ) {
		return true;
	}

	if ( flag.force_disabled ) {
		return false;
	}

	if ( flag.is_published ) {
		return true;
	}

	if ( currentUserId && flag.users && flag.users.length ) {
		return flag.users.includes( currentUserId.toString() );
	}

	return false;
};

export const getCurrentUserId = async () =>
	select( CoreStore ).getCurrentUser()?.id;
