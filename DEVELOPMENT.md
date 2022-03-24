# Development

This is a guide for software engineers who wish to take part in the development of this plugin.


## Quick Start

If you just want to bring down the repository and get a zip of the plugin for uploading to a WordPress install you can do so with the following steps:

1. `git clone git@github.com:boxuk/wp-plugin-skeleton.git`
2. `cd wp-plugin-skeleton`
3. `bin/prepare-zip.sh` (requires [composer](https://getcomposer.org))

This will create a `boxuk-skeleton-plugin.zip` file in the current directory which you can then use to upload to a WordPress install.

## Set up

There are two main ways to setup the plugin for development.

* [Just the plugin](#just-the-plugin)
* [Within a WordPress shell](#within-a-wordpress-shell)

### Just the plugin

It can be useful to set-up the plugin as a standalone library to just run the tests or to run static analysis.

* Clone the repository `git clone https://github.com/boxuk/wp-plugin-skeleton`
* Run [composer](https://getcomposer.org/) `composer install`
* Install the tools `composer install-tools`
> This will install PHP CodeSniffer within the tools directory.

### Within a WordPress shell

Often you will need to setup the plugin within a WordPress shell. This means, have the plugin within the `plugins` directory of a WordPress install.
You can either clone directly into the `plugins` directory of your WordPress site and work from within that directory, or take an approach similar to the following:

* Use a directory outside of the `plugins` directory, such as the `local-plugins` at the root of your project
* Copy across the files from this directory into yor `plugins` directory
* You can copy manually or through a custom script or you can use composer
* If you're using composer you can set your `local-plugins` directory to be a repository like so:

```json
    "repositories": [
        {
            "type": "composer",
            "url": "https://wpackagist.org"
        },
        {
            "options": {
                "symlink": false
            },
            "type": "path",
            "url": "./local-plugins/*"
        }
    ]
```

You can then install the plugin as you would a regular composer library, e.g. `composer req boxuk/wp-plugin-skeleton`
it can be useful to remove the plugin first for updates to ensure you're getting the latest and greatest, and thus a script in the main `composer.json`
such as the following is useful:

```json
    "scripts": {
        "update-plugin": [
            "rm -rf wp-content/plugins/wp-plugin-skeleton",
            "@composer update boxuk/wp-plugin-skeleton"
        ]
    }
```

## Static Analysis

### PHP CodeSniffer

You can run PHP Code Sniffer against the plugin code using the following command:

`composer phpcs`

You will find the list of standards we support and any exclusions we make within `phpcs.xml.dist` here is a quick breakdown:

* PHPCompatibilityWP
* WordPress-VIP-Go
* WordPress-Extra
* NeutronStandard
* WordPress-Docs

## Testing

### Unit tests

We run unit tests against anything that doesn't touch WordPress directly (including WordPress specific functions, e.g. `__()`)
You can use the following command to run the unit tests:

`composer test:unit`

Unit tests should be added within `tests/Unit` and follow the directory structure of the `src` directory as much as possible.
We use [PHPUnit](https://phpunit.de/) for unit testing.

### Integration tests

We run integration tests to test anything that touches WordPress, this includes anything that gets saved to the database or anything that uses a core function (e.g. `__()`)
You can use the following command to run the integration tests:

`composer test:integration`

Integration tests should be added within `tests/Integration` and loosely follow the directory structure of the `src` directory as much as possible.
We use [WP-PHPUnit](https://github.com/wp-phpunit) for integration testing.

## Distribution

To prepare a zip file of the plugin for distribution you can use the following command:

`bin/prepare-zip.sh`

This will create a `boxuk-skeleton-plugin.zip` file in the root directory which can then be used to upload to a standard WordPress installation within Add New -> Upload Plugin.

## Using rector

At time of writing we recommend targeting PHP 7.2 as the minimum version for a plugin. This is the sweet spot between wide adoption
and strong PHP features. However, it is still a little behind the latest stable version of PHP.

If you'd prefer to write code using syntax support by later versions of PHP though, you can do so. 

You must remember to 'downgrade' the code though before pushing (else the build on 7.2 could fail).

You can do this using [rector](https://getrector.org/) with the following command.

`composer downgrade`

This will automatically downgrade any code that is not compatible with PHP 7.2. There is a caveat though.

The format of the code will likely fail the PHPCS checks as rector will generate code that is PSR-(1)2 compliant rather than 
adhering to the WordPress coding standards. You can run the following which should fix most of the issues:

`composer phpcs:fix`

However, this may not get everything so manual inspection and correcting may be required. This is also the reason we don't
run this as part of CI.

> Note: You will need to run rector with the PHP version you have written the code for. For example, if you've written PHP 8.0 code
> and want to downgrade to PHP 7.2 you will need to run rector using PHP 8.0.
