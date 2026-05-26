<?php
/**
 * Plugin uninstall handler.
 *
 * Runs when the plugin is deleted via Plugins → Delete.
 * Removes all plugin options and the custom capability from WordPress roles.
 *
 * NOTE: Deactivation does NOT run this file. This only runs on hard deletion.
 *
 * @package GuloLinkInBio
 */

// Bail if not called by WordPress during plugin deletion.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Delete stored options.
delete_option( 'guloli_settings' );
delete_option( 'guloli_links' );

// Remove custom capability from all roles that might have it.
foreach ( array( 'administrator', 'editor' ) as $guloli_role_name ) {
	$guloli_role = get_role( $guloli_role_name );
	if ( $guloli_role instanceof WP_Role ) {
		$guloli_role->remove_cap( 'guloli_manage_settings' );
	}
}
