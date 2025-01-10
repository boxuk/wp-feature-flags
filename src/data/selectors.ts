/* Internal deps */
import { DEFAULT_STATE } from './default';

export const getFlags = ( state: typeof DEFAULT_STATE ) => state?.flags ?? [];
export const getGroups = ( state: typeof DEFAULT_STATE ) =>
	( state?.flags ?? [] )
		.map( ( flag ) => flag.group )
		.filter( ( group, index, groups ) => groups.indexOf( group ) === index )
		.filter( ( group ) => group !== undefined && group !== null )
		.sort();
