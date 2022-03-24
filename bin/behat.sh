#!/usr/bin/env bash

set -eo pipefail

# Specify the directory where the WordPress installation lives:
WP_CORE_DIR="${PWD}/tests/wp"

# Specify the URL for the site:
WP_URL="localhost:8000"

# Specify the name of the plugin, as could differ on build envs
PLUGIN_NAME="${PLUGIN_NAME:-wp-plugin-skeleton}"

# Shorthand:
WP="./vendor/bin/wp --color --path=$WP_CORE_DIR --url=http://$WP_URL"

# setup selenium
if [ ! -f ./bin/selenium-server-standalone-3.9.1.jar ]; then
    curl -L -o ./bin/selenium-server-standalone-3.9.1.jar http://selenium-release.storage.googleapis.com/3.9/selenium-server-standalone-3.9.1.jar 2>/dev/null &
    sleep 8
    chmod +x ./bin/selenium-server-standalone-3.9.1.jar
fi

# You need to have a java runtime setup, on MacOS the easiest way to do this is with `brew install temurin` see: https://stackoverflow.com/a/68779092/380054
java -jar ./bin/selenium-server-standalone-3.9.1.jar &> /dev/null &

java_pid=$!

# Start the PHP server:
php -S "$WP_URL" -t "$WP_CORE_DIR" -d disable_functions=mail &> /dev/null &

php_pid=$!

# Reset or install the test database:
$WP db reset --yes

# Install WordPress:
$WP core install --title="WP Site" --admin_user="admin" --admin_password="admin" --admin_email="admin@example.com" --skip-email

$WP plugin activate "$PLUGIN_NAME"

# Ensure plugin is enabled
$WP option update boxuk_enable_plugin 1

# Run the functional tests:
BEHAT_PARAMS='{"extensions" : {"PaulGibbs\\WordpressBehatExtension" : {"path" : "'$WP_CORE_DIR'"}}}' \
	./vendor/bin/behat --colors "$1"

# Stop webserver & selenium
kill $php_pid
kill $java_pid
