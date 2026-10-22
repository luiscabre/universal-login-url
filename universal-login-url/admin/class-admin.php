<?php
/**
 * Administration page controller.
 *
 * @package UniversalLoginURL
 */

namespace UniversalLoginURL\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the plugin settings page.
 *
 * @since 1.0.0
 */
final class Admin {

	/**
	 * Settings page slug.
	 *
	 * @var string
	 */
	private const PAGE_SLUG = 'universal-login-url';

	/**
	 * Settings page hook suffix.
	 *
	 * @var string
	 */
	private const PAGE_HOOK_SUFFIX = 'settings_page_universal-login-url';

	/**
	 * Settings manager.
	 *
	 * @var Settings
	 */
	private Settings $settings;

	/**
	 * Creates the admin controller.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->settings = new Settings();
	}

	/**
	 * Registers admin hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		$this->settings->init();

		add_action( 'admin_menu', array( $this, 'register_menu' ) );

		add_action(
			'admin_enqueue_scripts',
			array( $this, 'enqueue_admin_assets' )
		);
	}

	/**
	 * Adds the settings page under Settings.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		add_options_page(
			__( 'Universal Login URL', 'universal-login-url' ),
			__( 'Universal Login URL', 'universal-login-url' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Loads plugin styles only on its settings page.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_admin_assets( string $hook_suffix ): void {
		if ( self::PAGE_HOOK_SUFFIX !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'ulurl-admin',
			plugins_url( 'assets/css/admin.css', dirname( __FILE__ ) ),
			array(),
			'1.1.0'
		);
	}

	/**
	 * Renders the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__(
					'You are not allowed to access this page.',
					'universal-login-url'
				)
			);
		}
		?>
		<div class="wrap ulurl-settings-wrap">

			<header class="ulurl-page-header">
				<div class="ulurl-page-icon" aria-hidden="true">
					<span class="dashicons dashicons-lock"></span>
				</div>

				<div class="ulurl-page-heading">
					<h1>
						<?php
						esc_html_e(
							'Universal Login URL',
							'universal-login-url'
						);
						?>
					</h1>

					<p class="ulurl-page-description">
						<?php
						esc_html_e(
							'Manage your custom WordPress login address and review your access settings.',
							'universal-login-url'
						);
						?>
					</p>
				</div>
			</header>

			<?php settings_errors(); ?>

			<div class="ulurl-settings-layout">
				<section
					class="ulurl-settings-card"
					aria-labelledby="ulurl-settings-card-title"
				>
					<div class="ulurl-card-header">
						<div class="ulurl-card-heading-icon" aria-hidden="true">
							<span class="dashicons dashicons-admin-links"></span>
						</div>

						<div>
							<h2 id="ulurl-settings-card-title">
								<?php
								esc_html_e(
									'Custom Login Slug',
									'universal-login-url'
								);
								?>
							</h2>

							<p>
								<?php
								esc_html_e(
									'Choose the custom path you will use to access your WordPress login page.',
									'universal-login-url'
								);
								?>
							</p>
						</div>
					</div>

					<div class="ulurl-card-content">
						<form action="options.php" method="post">
							<?php
							settings_fields( 'ulurl_settings_group' );
							do_settings_sections( self::PAGE_SLUG );

							submit_button(
								__( 'Save Settings', 'universal-login-url' ),
								'primary ulurl-submit-button'
							);
							?>
						</form>
					</div>
				</section>

				<div class="ulurl-page-footer">
					<span class="dashicons dashicons-shield-alt" aria-hidden="true"></span>
					<p>
						<?php
						esc_html_e(
							'Keep your custom login URL in a safe place. A custom URL complements, but does not replace, strong passwords and regular backups.',
							'universal-login-url'
						);
						?>
					</p>
				</div>
			</div>
		</div>
		<?php
	}
}
