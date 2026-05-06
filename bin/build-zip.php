#!/usr/bin/env php
<?php
/**
 * Builds a versioned release ZIP via git archive.
 * Reads the version from the plugin header and names the file accordingly.
 * Run via: composer run package
 */

$root        = dirname( __DIR__ );
$plugin_file = $root . '/gulo-link-in-bio.php';

// Extract version from plugin header.
$content = file_get_contents( $plugin_file );
preg_match( '/\* Version:\s+(\S+)/', $content, $matches );
$version = $matches[1] ?? 'unknown';

$zip_name = "gulo-link-in-bio-{$version}.zip";
$zip_path = $root . DIRECTORY_SEPARATOR . $zip_name;

if ( file_exists( $zip_path ) ) {
	unlink( $zip_path );
}

// git archive respects .gitattributes export-ignore rules.
$cmd = sprintf(
	'git archive --format=zip --prefix=gulo-link-in-bio/ HEAD -o %s',
	escapeshellarg( $zip_path )
);

passthru( $cmd, $exit_code );

if ( 0 !== $exit_code ) {
	fwrite( STDERR, "ERROR: git archive failed (exit {$exit_code})\n" );
	exit( 1 );
}

$kb = round( filesize( $zip_path ) / 1024, 1 );
echo "Built: {$zip_name} ({$kb} KB)\n";
