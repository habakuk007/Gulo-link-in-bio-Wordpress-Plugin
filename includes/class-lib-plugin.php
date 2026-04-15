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
		// Register the page template immediately (not deferred to init) so the
		// block editor's REST API preload picks it up on first load.
		add_filter( 'theme_page_templates', array( 'LIB_Frontend', 'register_template' ) );

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
	 * @return void
	 */
	public function init(): void {
		new LIB_Frontend();

		if ( is_admin() ) {
			new LIB_Admin();
		}
	}

	/**
	 * Runs on plugin activation.
	 * Sets default options when none exist yet.
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
	}

	/**
	 * Runs on plugin deactivation.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		// Nothing to clean up — options are preserved on deactivation.
	}
}
