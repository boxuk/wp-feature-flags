import React from 'react';

import { createRoot } from 'react-dom/client';
import domReady from '@wordpress/dom-ready';

import { AdminInterface } from './AdminInterface';
import { registerStore } from './data';

const maybeLoadFixturesAdmin = () => {
	const root = document.getElementById( 'boxuk-wp-feature-flags' );
	if ( ! root ) {
		return;
	}

	registerStore();
	createRoot( root ).render( <AdminInterface /> );
};

domReady( maybeLoadFixturesAdmin );

export {
	useFlag,
	useFlags,
	useFlagEnabled,
	getFlag,
	getFlagEnabled,
	getFlags,
} from './data/hooks';
export { registerStore };
