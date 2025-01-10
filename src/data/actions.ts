/* WordPress deps */
import { apiFetch } from '@wordpress/data-controls';

/* Internal deps */
import { TYPES } from './constants';
import type { Flag } from '../types';
import { sanitizeFlag } from '../utils';

const { SET_FLAGS, SAVE_SUCCESS, SAVE_FAILURE } = TYPES;

function getFlagPath( flag: Flag ) {
	return 'feature-flags/v1/flags/' + flag.key;
}

export function setFlags( flags: Flag[] ) {
	return {
		type: SET_FLAGS,
		payload: flags,
	};
}

export function* updateFlag( flag: Flag ) {
	try {
		const response: unknown = yield apiFetch( {
			path: getFlagPath( flag ) + '',
			method: 'PUT',
			data: flag,
		} );

		if ( ! response || typeof response !== 'object' ) {
			throw new Error( 'Invalid response' );
		}

		return {
			type: SAVE_SUCCESS,
			payload: {
				flag: sanitizeFlag( response ),
			},
		};
	} catch ( error: unknown ) {
		console.error( error ); // eslint-disable-line no-console
		return {
			type: SAVE_FAILURE,
		};
	}
}
