import React from 'react';

import type { Fields } from '@wordpress/dataviews';
import { useSelect } from '@wordpress/data';

import { Flag } from '../types';

import { UsersRender } from '../components/UsersRender';
import { store } from '../data';
import { toDate } from '../utils';
import { BooleanRender } from '../components/BooleanRender';

export const useFields = (): Fields< Flag > => {
	const groups = useSelect( ( select ) => select( store ).getGroups(), [] );
	return [
		{
			id: 'key',
			label: 'Key',
			type: 'text',
		},
		{
			id: 'name',
			label: 'Name',
			enableSorting: true,
			enableGlobalSearch: true,
			enableHiding: true,
		},
		{
			id: 'description',
			label: 'Description',
			type: 'text',
			enableGlobalSearch: true,
		},
		{
			id: 'created',
			label: 'Created',
			type: 'datetime',
			enableSorting: true,
			render: ( row ) => toDate( row.item.created )?.toLocaleString(),
			getValue: ( { item } ) => toDate( item.created )?.getTime(),
		},
		{
			id: 'group',
			label: 'Group',
			type: 'text',
			enableGlobalSearch: true,
			enableSorting: true,
			filterBy: { isPrimary: true },
			elements: groups.map( ( group: string ) => ( {
				label: group,
				value: group,
			} ) ),
		},
		{
			id: 'state',
			label: 'State',
			render: ( row ) => <BooleanRender flag={ row.item } />,
			elements: [
				{
					label: 'Published',
					value: 'published',
				},
				{
					label: 'Partially Published',
					value: 'partially-published',
				},
				{
					label: 'Enforced',
					value: 'enforced',
				},
				{
					label: 'Disabled',
					value: 'disabled',
				},
				{
					label: 'Unpublished',
					value: 'unpublished',
				},
			],
			getValue: ( { item } ) => {
				if ( item.force_enabled ) {
					return 'enforced';
				}
				if ( item.force_disabled ) {
					return 'disabled';
				}
				if ( ! item.is_published && ( item.users?.length ?? 0 ) > 0 ) {
					return 'partially-published';
				}
				if ( item.is_published ) {
					return 'published';
				}
				if ( ! item.is_published ) {
					return 'unpublished';
				}
				return '';
			},
			filterBy: { isPrimary: true },
		},
		{
			id: 'users',
			label: 'Users',
			enableSorting: false,
			render: ( row ) => <UsersRender flag={ row.item } />,
		},
	];
};
