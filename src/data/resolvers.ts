/* WordPress deps */
import { apiFetch } from '@wordpress/data-controls';
import { setFlags } from './actions';
import { Flag } from '../types';

export function* getFlags() {
	const data: Flag[] = yield apiFetch( { path: 'feature-flags/v1/flags' } );
	return setFlags( data );
}
