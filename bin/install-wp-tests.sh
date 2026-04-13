#!/usr/bin/env bash
# install-wp-tests.sh
#
# Downloads and configures the WordPress test suite for PHPUnit.
# Usage: bash bin/install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]
#
# Based on the official WP develop scaffolding script.

DB_NAME=${1:-wordpress_test}
DB_USER=${2:-root}
DB_PASS=${3:-}
DB_HOST=${4:-localhost}
WP_VERSION=${5:-latest}

WP_TESTS_DIR=${WP_TESTS_DIR:-/tmp/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR:-/tmp/wordpress}

set -ex

install_wp() {
	if [ -d "$WP_CORE_DIR" ]; then
		return
	fi
	mkdir -p "$WP_CORE_DIR"

	if [ "$WP_VERSION" = 'latest' ]; then
		local ARCHIVE_NAME='latest'
	else
		local ARCHIVE_NAME="wordpress-$WP_VERSION"
	fi

	wget -nv -O /tmp/wordpress.tar.gz "https://wordpress.org/${ARCHIVE_NAME}.tar.gz"
	tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C "$WP_CORE_DIR"
}

install_test_suite() {
	if [ -d "$WP_TESTS_DIR" ]; then
		return
	fi
	mkdir -p "$WP_TESTS_DIR"

	if [ "$WP_VERSION" = 'latest' ]; then
		local TAG='trunk'
	else
		local TAG="tags/$WP_VERSION"
	fi

	svn export --quiet \
		"https://develop.svn.wordpress.org/${TAG}/tests/phpunit/includes/" \
		"$WP_TESTS_DIR/includes"

	svn export --quiet \
		"https://develop.svn.wordpress.org/${TAG}/tests/phpunit/data/" \
		"$WP_TESTS_DIR/data"

	wget -nv -O "$WP_TESTS_DIR/wp-tests-config.php" \
		"https://develop.svn.wordpress.org/${TAG}/wp-tests-config-sample.php"

	# Patch config
	sed -i "s|dirname( __FILE__ ) . '/src/'|'$WP_CORE_DIR/'|" "$WP_TESTS_DIR/wp-tests-config.php"
	sed -i "s/youremptytestdbnamehere/$DB_NAME/"             "$WP_TESTS_DIR/wp-tests-config.php"
	sed -i "s/yourusernamehere/$DB_USER/"                    "$WP_TESTS_DIR/wp-tests-config.php"
	sed -i "s/yourpasswordhere/$DB_PASS/"                    "$WP_TESTS_DIR/wp-tests-config.php"
	sed -i "s|localhost|$DB_HOST|"                           "$WP_TESTS_DIR/wp-tests-config.php"
}

create_db() {
	mysqladmin create "$DB_NAME" --user="$DB_USER" --password="$DB_PASS" \
		--host="$DB_HOST" --protocol=tcp 2>/dev/null || true
}

install_wp
install_test_suite
create_db
