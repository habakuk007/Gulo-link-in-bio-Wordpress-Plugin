<?php
/**
 * Plugin Name:       Gulo Link-in-Bio
 * Plugin URI:        https://github.com/habakuk007/Wordpress-LinkInBio-Template
 * Description:       A link-in-bio page for WordPress — a self-hosted alternative to Linktree. Configure your profile in the Gulo Link-in-Bio admin menu, then assign the page template to any Page.
 * Version:           0.0.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stefan Wagner
 * Author URI:        https://trumpkin.de/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gulo-link-in-bio
 * Domain Path:       /languages
 *
 * @package GuloLinkInBio
 */

defined( 'ABSPATH' ) || exit;

define( 'GULO_VERSION', '0.0.1' );
define( 'GULO_PLUGIN_FILE', __FILE__ );
define( 'GULO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GULO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GULO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once GULO_PLUGIN_DIR . 'includes/class-gulo-settings.php';
require_once GULO_PLUGIN_DIR . 'includes/class-gulo-frontend.php';
require_once GULO_PLUGIN_DIR . 'includes/class-gulo-admin.php';
require_once GULO_PLUGIN_DIR . 'includes/class-gulo-plugin.php';

register_activation_hook( __FILE__, array( 'GULO_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'GULO_Plugin', 'deactivate' ) );

GULO_Plugin::get_instance();
