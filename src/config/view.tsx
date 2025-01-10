import type { View, SupportedLayouts } from '@wordpress/dataviews';

export const defaultView: View = {
	fields: [ 'name', 'description', 'group', 'created', 'status', 'users' ],
	filters: [],
	page: 1,
	perPage: 20,
	search: '',
	type: 'table',
};

export const layouts: SupportedLayouts = {
	table: {
		fields: [ 'key', 'name', 'description', 'created', 'status', 'users' ],
		layout: {
			combinedFields: [
				{
					id: 'status',
					label: 'Status',
					children: [
						'is_published',
						'force_enabled',
						'force_disabled',
					],
					direction: 'horizontal',
				},
			],
		},
	},
};
