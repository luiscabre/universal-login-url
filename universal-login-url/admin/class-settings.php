<?php
/**
 * Plugin settings.
 *
 * @package UniversalLoginURL
 */

namespace UniversalLoginURL\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and validates plugin settings.
 *
 * @since 1.0.0
 */
final class Settings {

	/**
	 * Option name stored in the database.
	 *
	 * @var string
	 */
	public const OPTION_NAME = 'ulurl_login_slug';

	/**
	 * Settings group.
	 *
	 * @var string
	 */
	private const SETTINGS_GROUP = 'ulurl_settings_group';

	/**
	 * User meta key for the login URL notice.
	 *
	 * @var string
	 */
	private const NOTICE_META_KEY = 'ulurl_login_slug_notice';

	/**
	 * Registers settings hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_action(
			'update_option_' . self::OPTION_NAME,
			array( $this, 'record_login_slug_change' ),
			10,
			3
		);

		add_action(
			'add_option_' . self::OPTION_NAME,
			array( $this, 'record_initial_login_slug' ),
			10,
			2
		);

		add_action(
			'admin_notices',
			array( $this, 'render_login_slug_notice' )
		);
	}

	/**
	 * Registers the login slug setting and field.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting(
			self::SETTINGS_GROUP,
			self::OPTION_NAME,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_login_slug' ),
				'default'           => ULURL_DEFAULT_LOGIN_SLUG,
			)
		);

		/*
		 * Register the settings section without a heading or description.
		 *
		 * The page template already provides the visual card heading
		 * and description, so repeating them here is unnecessary.
		 */
		add_settings_section(
			'ulurl_general_section',
			'',
			false,
			'universal-login-url'
		);

		add_settings_field(
			self::OPTION_NAME,
			__( 'Custom login slug', 'universal-login-url' ),
			array( $this, 'render_login_slug_field' ),
			'universal-login-url',
			'ulurl_general_section'
		);
	}

	/**
	 * Sanitizes and validates the login slug.
	 *
	 * An empty value disables the custom login route.
	 *
	 * @param mixed $value Submitted value.
	 * @return string Valid slug or the previously stored slug if invalid.
	 */
	public function sanitize_login_slug( $value ): string {
		$current_value = get_option(
			self::OPTION_NAME,
			ULURL_DEFAULT_LOGIN_SLUG
		);

		if ( ! is_string( $value ) ) {
			add_settings_error(
				self::OPTION_NAME,
				'ulurl_invalid_slug',
				__(
					'The login slug must be a text value.',
					'universal-login-url'
				)
			);

			return is_string( $current_value ) ? $current_value : '';
		}

		$value = strtolower( trim( sanitize_text_field( $value ) ) );

		/*
		 * An empty value disables the custom login route.
		 */
		if ( '' === $value ) {
			return '';
		}

		if (
			! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value ) ||
			$this->is_reserved_slug( $value )
		) {
			add_settings_error(
				self::OPTION_NAME,
				'ulurl_invalid_slug',
				__(
					'Enter a valid slug using lowercase letters, numbers, and single hyphens. The slug must not conflict with a reserved route.',
					'universal-login-url'
				)
			);

			return is_string( $current_value ) ? $current_value : '';
		}

		return $value;
	}

	/**
	 * Checks whether the slug is reserved.
	 *
	 * @param string $slug Slug to check.
	 * @return bool
	 */
	private function is_reserved_slug( string $slug ): bool {
		$reserved_slugs = array(
			'wp-admin',
			'wp-login',
			'wp-login-php',
			'wp-content',
			'wp-includes',
			'wp-json',
			'admin',
			'login',
			'logout',
			'register',
			'password-reset',
		);

		return in_array( $slug, $reserved_slugs, true );
	}

	/**
	 * Records a successfully updated login slug for the current user.
	 *
	 * @param mixed  $old_value Previous option value.
	 * @param mixed  $new_value New option value.
	 * @param string $option    Option name.
	 * @return void
	 */
	public function record_login_slug_change(
		$old_value,
		$new_value,
		string $option
	): void {
		unset( $option );

		if (
			! is_string( $old_value ) ||
			! is_string( $new_value ) ||
			'' === $new_value ||
			$old_value === $new_value
		) {
			return;
		}

		$this->store_login_slug_notice( $new_value );
	}

	/**
	 * Records the login slug when the option is first created.
	 *
	 * @param string $option Option name.
	 * @param mixed  $value  New option value.
	 * @return void
	 */
	public function record_initial_login_slug(
		string $option,
		$value
	): void {
		unset( $option );

		if ( ! is_string( $value ) || '' === $value ) {
			return;
		}

		$this->store_login_slug_notice( $value );
	}

	/**
	 * Stores a notice for the current administrator.
	 *
	 * @param string $slug Configured login slug.
	 * @return void
	 */
	private function store_login_slug_notice( string $slug ): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		update_user_meta(
			get_current_user_id(),
			self::NOTICE_META_KEY,
			$slug
		);
	}

	/**
	 * Displays the login URL notice on the plugin settings page.
	 *
	 * @return void
	 */
	public function render_login_slug_notice(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if (
			! isset( $_GET['page'] ) ||
			! is_string( $_GET['page'] ) ||
			'universal-login-url' !== sanitize_key(
				wp_unslash( $_GET['page'] )
			)
		) {
			return;
		}

		$user_id = get_current_user_id();
		$slug    = get_user_meta(
			$user_id,
			self::NOTICE_META_KEY,
			true
		);

		if (
			! is_string( $slug ) ||
			! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug )
		) {
			return;
		}

		$login_url = home_url( '/' . rawurlencode( $slug ) . '/' );

		/*
		 * Display the notice only once for the administrator.
		 */
		delete_user_meta( $user_id, self::NOTICE_META_KEY );
		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<strong>
					<?php
					esc_html_e(
						'Your custom login URL has been updated.',
						'universal-login-url'
					);
					?>
				</strong>
			</p>

			<p>
				<?php
				esc_html_e(
					'Your new login URL is:',
					'universal-login-url'
				);
				?>
				<a href="<?php echo esc_url( $login_url ); ?>">
					<?php echo esc_html( $login_url ); ?>
				</a>
			</p>

			<p>
				<?php
				esc_html_e(
					'Save this URL in a password manager or another secure location before logging out. You will need it to access your account again.',
					'universal-login-url'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Renders the login slug input and permanent security notice.
	 *
	 * @return void
	 */
	public function render_login_slug_field(): void {
		$value = get_option(
			self::OPTION_NAME,
			ULURL_DEFAULT_LOGIN_SLUG
		);

		$value = is_string( $value ) ? $value : '';

		$is_configured = (
			'' !== $value &&
			(bool) preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value )
		);

		printf(
			'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text" placeholder="%3$s" autocomplete="off" spellcheck="false" />',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $value ),
			esc_attr__( 'Enter your custom slug', 'universal-login-url' )
		);

		echo '<p class="description">';

		esc_html_e(
			'For example, you can choose a custom path such as custom-login, private-access, or something similar. Use lowercase letters, numbers, and hyphens.',
			'universal-login-url'
		);

		echo '</p>';

		/*
		 * Show the URL preview only when a slug is configured.
		 */
		if ( $is_configured ) {
			$login_url = home_url(
				'/' . rawurlencode( $value ) . '/'
			);
			?>
			<div class="ulurl-login-url-preview">
				<strong>
					<?php
					esc_html_e(
						'Configured login URL preview',
						'universal-login-url'
					);
					?>
				</strong>

				<a href="<?php echo esc_url( $login_url ); ?>">
					<?php echo esc_html( $login_url ); ?>
				</a>
			</div>
			<?php
		}
		?>

		<div
			class="ulurl-security-notice"
			role="note"
			aria-labelledby="ulurl-security-notice-title"
		>
			<h3 id="ulurl-security-notice-title">
				<span
					class="dashicons dashicons-shield-alt"
					aria-hidden="true"
				></span>
				<?php
				esc_html_e(
					'Important Security Notice',
					'universal-login-url'
				);
				?>
			</h3>

			<p>
				<?php
				esc_html_e(
					'Your custom login URL is the entry point to your WordPress administration area. Once you configure a custom login slug, this URL becomes your designated login address for accessing your WordPress dashboard.',
					'universal-login-url'
				);
				?>
			</p>

			<p>
				<strong>
					<?php
					esc_html_e(
						'Before saving your changes or logging out, write down the custom slug you enter in the field above and save the complete login URL in a secure location.',
						'universal-login-url'
					);
					?>
				</strong>
			</p>

			<p>
				<strong>
					<?php
					esc_html_e(
						'Important:',
						'universal-login-url'
					);
					?>
				</strong>
				<?php
				esc_html_e(
					'If you forget your custom login URL, you may lose access to the WordPress dashboard until you complete the appropriate recovery procedure.',
					'universal-login-url'
				);
				?>
			</p>

			<p>
				<?php
				esc_html_e(
					'A custom login URL is not a substitute for strong passwords, regular backups, and other security measures.',
					'universal-login-url'
				);
				?>
			</p>
		</div>
		<?php
	}
}
