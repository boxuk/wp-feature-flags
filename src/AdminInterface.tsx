import React from 'react';

import {
	DataViews,
	filterSortAndPaginate,
	type View,
} from '@wordpress/dataviews';

import { actions, defaultView, defaultLayouts, useFields } from './config';
import { useFlags } from './data/hooks';
import { useLocalStorageState } from './utils/useLocalStorageState';

import './styles.scss';

export const AdminInterface = () => {
	const [ view, setView ] = useLocalStorageState< View >(
		'featureFlagsView',
		defaultView
	);

	const flags = useFlags();
	const fields = useFields();

	const { data, paginationInfo } = filterSortAndPaginate(
		flags ?? [],
		view,
		fields
	);

	return (
		<>
			<DataViews
				isLoading={ undefined === flags }
				actions={ actions }
				defaultLayouts={ defaultLayouts }
				fields={ fields }
				view={ view }
				data={ data }
				paginationInfo={ paginationInfo }
				onChangeView={ setView }
				getItemId={ ( item ) => ( item.key || 0 ).toString() }
			/>
		</>
	);
};
