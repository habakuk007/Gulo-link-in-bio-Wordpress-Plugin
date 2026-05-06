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
delete_option( 'gulo_settings' );
delete_option( 'gulo_links' );

// Remove custom capability from all roles that might have it.
foreach ( array( 'administrator', 'editor' ) as $gulo_role_name ) {
	$gulo_role = get_role( $gulo_role_name );
	if ( $gulo_role instanceof WP_Role ) {
		$gulo_role->remove_cap( 'gulo_manage_settings' );
	}
}
