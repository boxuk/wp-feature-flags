import { useSelect } from '@wordpress/data';
import { store as CoreStore, type User } from '@wordpress/core-data';

export const useUsers = () =>
	useSelect(
		( select ) =>
			select( CoreStore ).getEntityRecords< User< 'edit' > >(
				'root',
				'user',
				{ context: 'edit' }
			),
		[]
	);

export const useUser = ( userId: string ) =>
	useSelect(
		( select ) =>
			select( CoreStore ).getEntityRecord< User< 'edit' > >(
				'root',
				'user',
				userId
			),
		[ userId ]
	);
