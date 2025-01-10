import React, { Fragment, useState } from 'react';

import {
	Button,
	ButtonGroup,
	CheckboxControl,
	Flex,
	__experimentalGrid as Grid, // eslint-disable-line @wordpress/no-unsafe-wp-apis
} from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import type { User } from '@wordpress/core-data';
import type { RenderModalProps } from '@wordpress/dataviews';

import type { Flag } from '../types';
import { useUsers } from '../utils/useUsers';
import { store } from '../data';

export const UsersModal = ( {
	items,
	closeModal,
}: RenderModalProps< Flag > ) => {
	const [ flags, setFlags ] = useState( items );
	const { updateFlag } = useDispatch( store );
	const users = useUsers();

	const handleSave = () => {
		flags.forEach( updateFlag );
		if ( closeModal ) {
			closeModal();
		}
	};

	const toggleItem = ( item: Flag, user: User ) => ( value: boolean ) => {
		const newFlags = flags.map( ( flag ) => {
			if ( flag.key !== item.key ) {
				return flag;
			}

			return {
				...flag,
				users: value
					? [ ...( flag.users ?? [] ), user.id + '' ]
					: flag.users?.filter( ( id ) => id !== user.id + '' ),
			};
		} );

		setFlags( newFlags );
	};

	return (
		<>
			<Grid columns={ flags.length + 1 }>
				<>
					<h4>User</h4>
					{ flags.map( ( item ) => (
						<h4 key={ item.key }>{ item.name }</h4>
					) ) }
				</>
				{ users?.map( ( user ) => (
					<Fragment key={ user.id }>
						<span>{ user.email }</span>
						{ flags.map( ( item ) => (
							<CheckboxControl
								key={ item.key }
								aria-label={
									'Toggle ' + item.name + ' for ' + user.name
								}
								checked={ item.users?.includes( user.id + '' ) }
								onChange={ toggleItem( item, user ) }
							/>
						) ) }
					</Fragment>
				) ) }
			</Grid>
			<Flex justify="end" style={ { marginTop: '1rem' } }>
				<ButtonGroup>
					<Button
						variant="secondary"
						isDestructive
						onClick={ closeModal }
					>
						Cancel
					</Button>
					<Button variant="primary" onClick={ handleSave }>
						Save
					</Button>
				</ButtonGroup>
			</Flex>
		</>
	);
};
