<?php
/**
 * Plugin Name:       Link in Bio
 * Plugin URI:        https://github.com/stefanwagner/wordpress-link-in-bio
 * Description:       A LinkTree-style link page for WordPress. Configure your profile in Settings → Link in Bio, then assign the page template to any Page.
 * Version:           1.0.0-alpha.8
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stefan Wagner
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       link-in-bio
 * Domain Path:       /languages
 *
 * @package LinkInBio
 */

defined( 'ABSPATH' ) || exit;

define( 'LIB_VERSION', '1.0.0-alpha.8' );
define( 'LIB_PLUGIN_FILE', __FILE__ );
define( 'LIB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LIB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LIB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once LIB_PLUGIN_DIR . 'includes/class-lib-settings.php';
require_once LIB_PLUGIN_DIR . 'includes/class-lib-frontend.php';
require_once LIB_PLUGIN_DIR . 'includes/class-lib-admin.php';
require_once LIB_PLUGIN_DIR . 'includes/class-lib-plugin.php';

register_activation_hook( __FILE__, array( 'LIB_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'LIB_Plugin', 'deactivate' ) );

LIB_Plugin::get_instance();
