#!/usr/bin/env bash

# Usage: ./bin/bump_version.sh <major|minor|patch> - Increments the relevant version part by one.
# You can also pass a second argument <main_plugin_filename> to use a custom main plugin filename.

set -e

main_plugin_file="${2:-wp-plugin-skeleton.php}"

if [ "$1" == "major" ] || [ "$1" == "minor" ] || [ "$1" == "patch" ]; then

	current_version=$(grep -E -o '^Stable tag: (.+)' readme.txt | cut -d" " -f3)

	IFS='.' read -a version_parts <<< "$current_version"

	major=${version_parts[0]}
	minor=${version_parts[1]}
	patch=${version_parts[2]}

	case "$1" in
		"major")
			major=$((major + 1))
			minor=0
			patch=0
			;;
		"minor")
			minor=$((minor + 1))
			patch=0
			;;
		"patch")
			patch=$((patch + 1))
			;;
	esac
	new_version="$major.$minor.$patch"

	echo "Bumping stable tag in readme.txt to $new_version"
	sed -i '' -E "s/^Stable tag: .+/Stable tag: $new_version/" readme.txt

		echo "Bumping version docblock in $main_plugin_file to $new_version"
		sed -i '' -E "s/^ \* Version: .+/ \* Version: $new_version/" $main_plugin_file

	echo "Bumping version constant in $main_plugin_file to $new_version"
	sed -i '' -E "s/^define\( '(.+)_VERSION', '.+' \);/define\( '\1_VERSION', '$new_version' \);/" $main_plugin_file

	echo "Done."
else
	echo "Usage: ./bin/bump_version.sh <major|minor|patch> - Increments the relevant version part by one. (<main_plugin_filename> - optionally set a custom main plugin filename)"
fi
