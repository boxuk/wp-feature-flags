import React from 'react';

import type { Action } from '@wordpress/dataviews';
import { dispatch } from '@wordpress/data';
import { store } from '../data';

import { Flag } from '../types';
import { UsersModal } from '../components/UsersModal';
import { canModifyPublishState } from '../utils';

import { ReactComponent as Check } from '../icons/check.svg';
import { ReactComponent as ManageUsers } from '../icons/manage-users.svg';
import { ReactComponent as Cross } from '../icons/cross.svg';

export const actions: Action< Flag >[] = [
	{
		id: 'pubish',
		label: 'Publish',
		isPrimary: true,
		icon: <Check />,
		supportsBulk: true,
		isEligible: ( item ) =>
			!! ( ! item.is_published && canModifyPublishState( item ) ),
		callback: ( items ) => {
			items.map( ( item ) =>
				dispatch( store ).updateFlag( { ...item, is_published: true } )
			);
		},
		disabled: true,
	},
	{
		id: 'manage_users',
		label: 'Manage Flag Users',
		isPrimary: true,
		icon: <ManageUsers />,
		supportsBulk: true,
		isEligible: ( item ) => canModifyPublishState( item ),
		RenderModal: UsersModal,
	},
	{
		id: 'unpublish',
		label: 'Unpublish',
		isPrimary: true,
		icon: <Cross />,
		supportsBulk: true,
		isDestructive: true,
		isEligible: ( item ) =>
			!! ( item.is_published && canModifyPublishState( item ) ),
		callback: ( items ) => {
			items.map( ( item ) =>
				dispatch( store ).updateFlag( { ...item, is_published: false } )
			);
		},
	},
];
