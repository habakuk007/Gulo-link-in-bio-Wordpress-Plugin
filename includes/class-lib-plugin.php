<?php
/**
 * Main plugin bootstrap class.
 *
 * @package LinkInBio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class LIB_Plugin
 *
 * Singleton that bootstraps the plugin, wires hooks, and coordinates
 * the admin and frontend sub-systems.
 */
class LIB_Plugin {

	/**
	 * Single instance.
	 *
	 * @var LIB_Plugin|null
	 */
	private static ?LIB_Plugin $instance = null;

	/**
	 * Returns (and creates on first call) the singleton instance.
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Constructor — registers hooks. */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Loads the plugin text domain for i18n.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'link-in-bio',
			false,
			dirname( LIB_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Fires on WordPress 'init' — boots sub-systems.
	 *
	 * Also ensures the lib_manage_settings capability is present on existing
	 * installations that pre-date this feature (upgrade compatibility).
	 *
	 * @return void
	 */
	public function init(): void {
		// Grant the capability if it is missing (handles plugin upgrades).
		$role = get_role( 'administrator' );
		if ( $role instanceof WP_Role && ! $role->has_cap( 'lib_manage_settings' ) ) {
			self::grant_settings_capability();

			// Also update the current user's in-memory capability object so that
			// this same request benefits from the newly granted cap without a reload.
			$current_user = wp_get_current_user();
			if ( $current_user instanceof WP_User && $current_user->exists() ) {
				$current_user->add_cap( 'lib_manage_settings' );
			}
		}

		new LIB_Frontend();

		if ( is_admin() ) {
			new LIB_Admin();
		}
	}

	/**
	 * Runs on plugin activation.
	 * Sets default options when none exist yet and grants the settings capability.
	 *
	 * @return void
	 */
	public static function activate(): void {
		if ( false === get_option( LIB_Settings::OPTION_SETTINGS ) ) {
			update_option( LIB_Settings::OPTION_SETTINGS, LIB_Settings::get_defaults() );
		}

		if ( false === get_option( LIB_Settings::OPTION_LINKS ) ) {
			update_option( LIB_Settings::OPTION_LINKS, wp_json_encode( array() ) );
		}

		// WordPress caches the page template list per theme. Clear it so our
		// template appears in the dropdown immediately after activation.
		wp_cache_delete( 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() ), 'themes' );

		self::grant_settings_capability();
	}

	/**
	 * Runs on plugin deactivation.
	 * Removes the custom capability from all roles that held it.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		foreach ( array( 'administrator', 'editor' ) as $role_name ) {
			$role = get_role( $role_name );
			if ( $role instanceof WP_Role ) {
				$role->remove_cap( 'lib_manage_settings' );
			}
		}
	}

	/**
	 * Grants the lib_manage_settings capability to administrators and editors.
	 *
	 * @return void
	 */
	private static function grant_settings_capability(): void {
		foreach ( array( 'administrator', 'editor' ) as $role_name ) {
			$role = get_role( $role_name );
			if ( $role instanceof WP_Role ) {
				$role->add_cap( 'lib_manage_settings' );
			}
		}
	}
}
