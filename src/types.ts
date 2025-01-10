/**
 * Type defs for all the API responses.
 */

/**
 * A generic for a partial object that can have null or undefined values - default `Partial` does not include null.
 */
type PartialNullable< T > = { [ P in keyof T ]?: T[ P ] | undefined | null };

export type Flag = PartialNullable< {
	key: string;
	name: string;
	description: string;
	created: ApiDate;
	group: string;
	force_enabled: boolean;
	force_disabled: boolean;
	is_published: boolean;
	users: string[];
} >;

export type ApiDate = PartialNullable< {
	date: string;
	timezone_type: number;
	timezone: string;
} >;
