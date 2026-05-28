<?php
/**
 * Plugin Name:       Gulo Link-in-Bio
 * Plugin URI:        https://github.com/habakuk007/Gulo-link-in-bio-Wordpress-Plugin
 * Description:       A self-hosted link-in-bio page for WordPress. Configure your profile in the admin menu, then assign the template to any Page.
 * Version:           0.3.0
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

define( 'GULOLI_VERSION', '0.3.0' );
define( 'GULOLI_PLUGIN_FILE', __FILE__ );
define( 'GULOLI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GULOLI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GULOLI_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once GULOLI_PLUGIN_DIR . 'includes/class-guloli-settings.php';
require_once GULOLI_PLUGIN_DIR . 'includes/class-guloli-frontend.php';
require_once GULOLI_PLUGIN_DIR . 'includes/class-guloli-admin.php';
require_once GULOLI_PLUGIN_DIR . 'includes/class-guloli-plugin.php';

register_activation_hook( __FILE__, array( 'GULOLI_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'GULOLI_Plugin', 'deactivate' ) );

GULOLI_Plugin::get_instance();
