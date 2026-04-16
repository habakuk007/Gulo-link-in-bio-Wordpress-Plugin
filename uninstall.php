<?php
/**
 * Plugin uninstall handler.
 *
 * Runs when the plugin is deleted via Plugins → Delete.
 * Removes all plugin options and the custom capability from WordPress roles.
 *
 * NOTE: Deactivation does NOT run this file. This only runs on hard deletion.
 *
 * @package LinkInBio
 */

// Bail if not called by WordPress during plugin deletion.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Delete stored options.
delete_option( 'lib_settings' );
delete_option( 'lib_links' );

// Remove custom capability from all roles that might have it.
foreach ( array( 'administrator', 'editor' ) as $lib_role_name ) {
	$lib_role = get_role( $lib_role_name );
	if ( $lib_role instanceof WP_Role ) {
		$lib_role->remove_cap( 'lib_manage_settings' );
	}
}
