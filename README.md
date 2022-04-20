# WP Feature Flags

A plugin used to manage the publishing of features.

## Use case

At [BoxUK](https://boxuk.com), we release code behind feature flags. There are [lots of reasons](https://www.boxuk.com/insight/coding-with-feature-flags/) for this, but it mainly allows us to release code more frequently and at lower risk.

We use flags to slowly roll out functionality in increments, across environments. They also let us hide features without a code deploy if we spot bugs.

## Registering a flag

Registering flags is handled using the `wp_feature_flags_register` filter. This lets you access the array of flags and add your own.

A simple flag registration looks like this:

```
add_filter( 'wp_feature_flags_register_flags', 'test_flag_register' );

function test_flag_register( array $flag_register ): array {
	$flag_register['my-test-flag'] = [
		'name'        => 'My Test Flag',
		'description' => 'Using this flag to test the WP Feature Flags plugin.',
		'stable'      => true
	];

	return $flag_register;
}
```

This code accepts the current array of flags, which we're calling `$flag_register` in this example.

It adds a new flag with the key `my-test-flag` along with the absolute basic information needed to register a flag:

* **name (text string)** - the briefest possible summary of what the flag does
* **description (text string)** - a bit more detail about what the flag does, or why it exists
* **stable (true/false)** - whether the flag is stable enough to be published. This is false by default for safety

There are extra attributes that can be set in this array that give you more control over your flags.

* **group (text string)** - to make related flags easier to find, you can group them together by giving them the same group name
* **enforced (true/false)** - flags can be forced into a published state by setting this to true
* **meta (array)** - an array of extra attributes and labels

### Meta attributes

Adding a 'meta' array to your flag allows you to give flags more detailed labels and include links by adding an array of values with `label`, `value` and `link` keys.

Here's an example with meta values for the date a flag was added and the date we want to check if the flagged code has caused any issues.

```
add_filter( 'wp_feature_flags_register_flags', 'test_flag_register_flag_with_dates' );

function test_flag_register_flag_with_dates( array $flag_register ): array {
	$flag_register['test-flag-with-dates'] = [
		'name'        => 'Test Flag With Dates',
		'description' => 'A simple demo flag for the meta array',
		'stable'      => true,
		'meta'        => [
			[
				'label' => 'Date added',
				'value' => '22/4/2022',
			],
			[
				'label' => 'Review date',
				'value' => '6/5/2022',
			],
		],
	];

	return $flag_register;
}
```
You can add any pair of `label` and `value` here to suit your own use.

By including `link` alongside it, you can link to relevant web pages.

```
add_filter( 'wp_feature_flags_register_flags', 'test_flag_register_flag_with_links' );

function test_flag_register_flag_with_links( array $flag_register ): array {
	$flag_register['test-flag-with-links'] = [
		'name'        => 'Test Flag With Links',
		'description' => 'A simple demo flag for the meta array with links',
		'stable'      => true,
		'meta'        => [
			[
				'label' => 'GitHub issue',
				'value' => '#12 Basic documentation',
				'link' => 'https://github.com/boxuk/wp-feature-flags/issues/12',
			],
		],
	];

	return $flag_register;
}
```

Here, we've added a link to the GitHub issue we've created for this project about adding basic documentation.

## Altering multiple flags

Since the full array of flags is always passed to the `wp_feature_flags_register_flags` filter, you can use it to loop through it to make changes to all registered flags.

```
add_filter( 'wp_feature_flags_register_flags', 'test_flag_register_alterations', 99 );

function test_flag_register_alterations( array $flag_register ): array {
	$example_meta = [
		'label' => 'Example',
		'value' => 'This is added to every flag'
	];

	foreach ( $flag_register as $flag_key => $flag_val ) {
		array_push( $flag_register[ $flag_key ][ 'meta' ], $example_meta );
	}

	return $flag_register;
}
```

In our example, we've added some example data to the meta of every flag.  The same approach could be used to mark flags of a specific group as unstable.

## Development Documentation

See [DEVELOPMENT.md](DEVELOPMENT.md)

### Hooks

| Hook                     | Type   | Description                    |
|--------------------------|--------|--------------------------------|
| `wp_feature_flags_plugin_uninstall` | Action | Called during plugin uninstall. |
| `wp_feature_flags_register` | Filter | Used to register and amend flags |