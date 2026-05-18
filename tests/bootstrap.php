<?php
/**
 * PHPUnit bootstrap for the Gulo Link-in-Bio plugin.
 *
 * Requires the WordPress test suite. Set the WP_TESTS_DIR environment
 * variable or the WP_TESTS_DIR constant to the path of the suite.
 *
 * @package SimpleBioLinks
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' ) ?: '/tmp/wordpress-tests-lib';

// Fallback: wp-env (Docker) mounts the test suite here.
if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	$_tests_dir = '/wordpress-phpunit';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo 'ERROR: WordPress test suite not found.' . PHP_EOL;
	echo 'Options:' . PHP_EOL;
	echo '  1. Set WP_TESTS_DIR and run: bash bin/install-wp-tests.sh <db> <user> <pass>' . PHP_EOL;
	echo '  2. Install Docker and run: npx @wordpress/env start' . PHP_EOL;
	exit( 1 );
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin on muplugins_loaded so it is available
 * to every test case.
 */
tests_add_filter(
	'muplugins_loaded',
	static function (): void {
		require dirname( __DIR__ ) . '/simple-bio-links.php';
	}
);

require $_tests_dir . '/includes/bootstrap.php';
