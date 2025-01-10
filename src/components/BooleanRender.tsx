import React from 'react';

import { ReactComponent as Check } from '../icons/check.svg';
import { ReactComponent as Partial } from '../icons/partial.svg';
import { ReactComponent as Enforced } from '../icons/enforced.svg';
import { ReactComponent as Disabled } from '../icons/disabled.svg';
import { ReactComponent as Cross } from '../icons/cross.svg';

import { Flag } from '../types';
import { Flex } from '@wordpress/components';

export const BooleanRender = ( { flag }: { flag: Flag } ) => {
	if ( flag.force_enabled ) {
		return (
			<Flex justify={ 'start' }>
				<Enforced />
				<span>Enforced</span>
			</Flex>
		);
	}
	if ( flag.force_disabled ) {
		return (
			<Flex justify={ 'start' }>
				<Disabled />
				<span>Disabled</span>
			</Flex>
		);
	}

	if ( ! flag.is_published && ( flag.users?.length ?? 0 > 0 ) ) {
		return (
			<Flex justify={ 'start' }>
				<Partial />
				<span>Published for { flag.users?.length } users</span>
			</Flex>
		);
	}

	if ( flag.is_published ) {
		return (
			<Flex justify={ 'start' }>
				<Check />
				<span>Published</span>
			</Flex>
		);
	}

	if ( ! flag.is_published ) {
		return (
			<Flex justify={ 'start' }>
				<Cross />
				<span>Unpublished</span>
			</Flex>
		);
	}

	return null;
};
