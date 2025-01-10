/* Internal deps */
import { TYPES } from './constants';
import { DEFAULT_STATE } from './default';
import { sanitizeFlag } from '../utils';

/* Types */
const { SET_FLAGS, SAVE_SUCCESS, SAVE_FAILURE } = TYPES;

const reducer = (
	state: typeof DEFAULT_STATE,
	{
		type,
		payload,
	}: {
		type: string;
		payload: unknown;
	}
): typeof DEFAULT_STATE => {
	switch ( type ) {
		case SET_FLAGS:
			if ( ! Array.isArray( payload ) ) {
				return { ...state };
			}

			return {
				...state,
				flags: ( payload as unknown[] ).map( sanitizeFlag ),
			};

		case SAVE_SUCCESS:
			if (
				typeof payload !== 'object' ||
				! payload ||
				'flag' in payload === false ||
				! payload.flag
			) {
				return { ...state };
			}

			const newFlag = sanitizeFlag( payload.flag );
			return {
				...state,
				flags: state.flags.map( ( flag ) =>
					flag.key === newFlag.key ? newFlag : flag
				),
			};

		case SAVE_FAILURE:
		default:
			return state;
	}
};

export default reducer;
