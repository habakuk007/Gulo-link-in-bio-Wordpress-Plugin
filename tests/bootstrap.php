<?php
/**
 * PHPUnit bootstrap for the Link in Bio plugin.
 *
 * Requires the WordPress test suite. Set the WP_TESTS_DIR environment
 * variable or the WP_TESTS_DIR constant to the path of the suite.
 *
 * @package LinkInBio
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' ) ?: '/tmp/wordpress-tests-lib';

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo 'ERROR: WordPress test suite not found at ' . $_tests_dir . PHP_EOL;
	echo 'Set WP_TESTS_DIR to the path of the WordPress test library.' . PHP_EOL;
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
		require dirname( __DIR__ ) . '/link-in-bio.php';
	}
);

require $_tests_dir . '/includes/bootstrap.php';
