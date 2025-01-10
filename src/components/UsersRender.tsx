import React from 'react';

import type { Flag } from '../types';
import { useUser } from '../utils/useUsers';

export const UsersRender = ( { flag }: { flag: Flag } ) => {
	if ( ! flag.users || 0 === flag.users.length ) {
		return null;
	}

	return (
		<ul style={ { margin: 0 } }>
			{ flag.users?.map( ( userId ) => (
				<li key={ userId }>
					<UserRender userId={ userId } />
				</li>
			) ) }
		</ul>
	);
};

const UserRender = ( { userId }: { userId: string } ) => {
	const user = useUser( userId );
	return user?.email ?? '';
};
