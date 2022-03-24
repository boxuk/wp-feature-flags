#!/usr/bin/env bash

# Exit when any command fails
set -e

if ! [ -x "$(command -v zip)" ]; then
  echo 'Error: zip is not installed.' >&2
  exit 1
fi

# Remove contents of our vendor directory.
rm -rf vendor;
rm -f composer.lock;

# Remove dependencies we have prefixed.
composer remove composer/installers --update-no-dev

# Install non dev deps.
composer install --no-dev --optimize-autoloader

# Zip up the plugin excluding tests and tools.
zip -vr boxuk-skeleton-plugin.zip . -x "tests/*" -x "tools/*" -x "features/*" -x "DEVELOPMENT.md" -x "behat.yml" \
    -x "phpunit.xml.dist" -x "phpcs.xml.dist" -x "bin/*" -x ".git/*" -x ".DS_Store" -x ".github/*" \
    -x ".travis.yml" -x "docker-compose.yml" -x ".gitignore" -x "rector-downgrade.php"

# Let's restore the state of our project.
composer require composer/installers
composer install
