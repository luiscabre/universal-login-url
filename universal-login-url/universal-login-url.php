<?php
/**
 * Plugin Name: Universal Login URL
 * Plugin URI:  https://example.com/universal-login-url
 * Description: Configure a custom WordPress login path. Access protection is enabled only after the custom login route is configured.
 * Version:     1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.2
 * Author:      Luis Cabré
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: universal-login-url
 * Domain Path: /languages
 *
 * @package UniversalLoginURL
 */

namespace UniversalLoginURL;

defined( 'ABSPATH' ) || exit;

define( 'ULURL_VERSION', '1.0.0' );
define( 'ULURL_PLUGIN_FILE', __FILE__ );
define( 'ULURL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ULURL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ULURL_OPTION_NAME', 'ulurl_login_slug' );

/*
 * The administrator must configure a custom slug
 * before the custom login route is enabled.
 */
define( 'ULURL_DEFAULT_LOGIN_SLUG', '' );

require_once ULURL_PLUGIN_DIR . 'includes/class-plugin.php';

/**
 * Starts the plugin after all active plugins have loaded.
 *
 * @return void
 */
function ulurl_start_plugin(): void {
	load_plugin_textdomain(
		'universal-login-url',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);

	Plugin::get_instance()->init();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\ulurl_start_plugin' );

/**
 * Removes the custom login configuration on deactivation.
 *
 * The standard WordPress login remains available when the plugin
 * is deactivated or reactivated without a configured slug.
 *
 * @return void
 */
function ulurl_deactivate_plugin(): void {
	/*
	 * Remove the configured custom login slug.
	 */
	delete_option( ULURL_OPTION_NAME );

	/*
	 * Remove the auxiliary option that tracks the previous slug.
	 */
	delete_option( 'ulurl_rewrite_slug' );

	/*
	 * Invalidate cached rewrite rules. WordPress will rebuild
	 * them on a subsequent request without this plugin's route.
	 */
	delete_option( 'rewrite_rules' );
}
register_deactivation_hook(
	__FILE__,
	__NAMESPACE__ . '\\ulurl_deactivate_plugin'
);