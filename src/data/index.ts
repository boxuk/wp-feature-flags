/* WordPress deps */
import { controls } from '@wordpress/data-controls';
import { register, createReduxStore } from '@wordpress/data';

/* Internal deps */
import * as actions from './actions';
import { STORE_KEY } from './constants';
import reducer from './reducer';
import * as resolvers from './resolvers';
import * as selectors from './selectors';

export const store = createReduxStore( STORE_KEY, {
	selectors,
	actions,
	reducer,
	resolvers,
	controls,
} );

export const registerStore = () => {
	register( store );
};

export { STORE_KEY };
