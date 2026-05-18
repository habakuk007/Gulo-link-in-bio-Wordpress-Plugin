<?php
/**
 * Bootstrap for unit tests.
 *
 * No WordPress environment is required — Brain\Monkey stubs all WP functions.
 *
 * @package SimpleBioLinks
 */

// Satisfy the ABSPATH guard in every plugin file.
define( 'ABSPATH', '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WP core constant required by plugin guards.

// Composer autoloader (PHPUnit, Brain\Monkey, polyfills).
require dirname( __DIR__, 2 ) . '/vendor/autoload.php';

// Load only the classes under test — no full plugin bootstrap.
require dirname( __DIR__, 2 ) . '/includes/class-guloli-settings.php';
