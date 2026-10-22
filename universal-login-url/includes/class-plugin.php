<?php
/**
 * Main plugin class.
 *
 * @package UniversalLoginURL
 */

namespace UniversalLoginURL;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initializes the plugin and its components.
 */
final class Plugin {

	/**
	 * Plugin instance.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Whether the plugin has been initialized.
	 *
	 * @var bool
	 */
	private bool $initialized = false;

	/**
	 * Get the plugin instance.
	 *
	 * @return Plugin
	 */
	public static function get_instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		if ( $this->initialized ) {
			return;
		}

		$this->initialized = true;
		$this->register_hooks();
	}

	/**
	 * Register the plugin hooks.
	 *
	 * @return void
	 */
	private function register_hooks(): void {

		// Load and initialize the custom login route.
		require_once ULURL_PLUGIN_DIR . 'public/class-login-route.php';

		$login_route = new \UniversalLoginURL\Frontend\Login_Route();
		$login_route->init();

		// Load the admin settings only in the WordPress dashboard.
		if ( is_admin() ) {
			require_once ULURL_PLUGIN_DIR . 'admin/class-settings.php';
			require_once ULURL_PLUGIN_DIR . 'admin/class-admin.php';

			$admin = new Admin\Admin();
			$admin->init();
		}

		/**
		 * Fires after Universal Login URL has been initialized.
		 *
		 * @param Plugin $plugin The plugin instance.
		 */
		do_action( 'ulurl_initialized', $this );
	}
}