<?php
/**
 * Handles the custom login URL.
 *
 * @package UniversalLoginURL
 */

namespace UniversalLoginURL\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and handles the custom login route.
 */
final class Login_Route {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'register_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'handle_login_route' ) );
	}

	/**
	 * Register the custom login rewrite rule.
	 *
	 * @return void
	 */
	public function register_rewrite_rules(): void {
		$slug = get_option(
			'ulurl_login_slug',
			ULURL_DEFAULT_LOGIN_SLUG
		);

		if (
			! is_string( $slug ) ||
			! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug )
		) {
			$slug = ULURL_DEFAULT_LOGIN_SLUG;
		}

		add_rewrite_rule(
			'^' . preg_quote( $slug, '#' ) . '/?$',
			'index.php?ulurl_login=1',
			'top'
		);
	}

	/**
	 * Register the custom query variable.
	 *
	 * @param string[] $query_vars Public query variables.
	 * @return string[]
	 */
	public function register_query_vars( array $query_vars ): array {
		$query_vars[] = 'ulurl_login';

		return $query_vars;
	}

	/**
	 * Handle requests to the custom login URL.
	 *
	 * @return void
	 */
	public function handle_login_route(): void {
		if ( '1' !== get_query_var( 'ulurl_login' ) ) {
			return;
		}

		nocache_headers();

		$redirect_to = admin_url();

		if ( isset( $_REQUEST['redirect_to'] ) && is_string( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = esc_url_raw(
				wp_unslash( $_REQUEST['redirect_to'] )
			);
		}

		$redirect_to = wp_validate_redirect(
			$redirect_to,
			admin_url()
		);

		if ( is_user_logged_in() ) {
			wp_safe_redirect( $redirect_to );
			exit;
		}

		$error_message = '';
		$username      = '';

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			$nonce = '';

			if (
				isset( $_POST['ulurl_login_nonce'] ) &&
				is_string( $_POST['ulurl_login_nonce'] )
			) {
				$nonce = sanitize_text_field(
					wp_unslash( $_POST['ulurl_login_nonce'] )
				);
			}

			if ( ! wp_verify_nonce( $nonce, 'ulurl_login_action' ) ) {
				$error_message = __(
					'Security verification failed. Please reload the page and try again.',
					'universal-login-url'
				);
			} else {
				$login = '';
				$password = '';
				$remember = false;

				if (
					isset( $_POST['log'] ) &&
					is_string( $_POST['log'] )
				) {
					$login = sanitize_text_field(
						wp_unslash( $_POST['log'] )
					);
				}

				if (
					isset( $_POST['pwd'] ) &&
					is_string( $_POST['pwd'] )
				) {
					$password = wp_unslash( $_POST['pwd'] );
				}

				if (
					isset( $_POST['rememberme'] ) &&
					'forever' === $_POST['rememberme']
				) {
					$remember = true;
				}

				$username = $login;

				$credentials = array(
					'user_login'    => $login,
					'user_password' => $password,
					'remember'      => $remember,
				);

				$user = wp_signon( $credentials, is_ssl() );

				if ( is_wp_error( $user ) ) {
					$error_message = __(
						'Login failed. Please check your username and password.',
						'universal-login-url'
					);
				} else {
					wp_safe_redirect( $redirect_to );
					exit;
				}
			}
		}

		$this->render_login_form(
			$error_message,
			$username,
			$redirect_to
		);

		exit;
	}

	/**
	 * Render the login form.
	 *
	 * @param string $error_message Login error message.
	 * @param string $username      Previously submitted username.
	 * @param string $redirect_to   Destination after login.
	 * @return void
	 */
	private function render_login_form(
		string $error_message,
		string $username,
		string $redirect_to
	): void {
		$slug = get_option(
			'ulurl_login_slug',
			ULURL_DEFAULT_LOGIN_SLUG
		);

		if (
			! is_string( $slug ) ||
			! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug )
		) {
			$slug = ULURL_DEFAULT_LOGIN_SLUG;
		}

		$login_url = home_url( '/' . $slug . '/' );

		status_header( 200 );
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>
				<?php esc_html_e( 'Log In', 'universal-login-url' ); ?>
			</title>
			<style>
				body {
					margin: 0;
					padding: 40px 20px;
					background: #f0f0f1;
					color: #3c434a;
					font-family: Arial, sans-serif;
				}

				.ulurl-login {
					max-width: 360px;
					margin: 40px auto;
					padding: 28px;
					background: #fff;
					border: 1px solid #dcdcde;
					border-radius: 8px;
					box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
				}

				.ulurl-login h1 {
					margin: 0 0 24px;
					font-size: 25px;
					text-align: center;
				}

				.ulurl-login label {
					display: block;
					margin: 16px 0 6px;
					font-weight: 600;
				}

				.ulurl-login input[type="text"],
				.ulurl-login input[type="password"] {
					box-sizing: border-box;
					width: 100%;
					padding: 11px;
					border: 1px solid #8c8f94;
					border-radius: 4px;
				}

				.ulurl-login .ulurl-remember {
					margin: 16px 0;
				}

				.ulurl-login button {
					width: 100%;
					padding: 12px;
					border: 0;
					border-radius: 4px;
					background: #2271b1;
					color: #fff;
					font-size: 16px;
					cursor: pointer;
				}

				.ulurl-login button:hover {
					background: #135e96;
				}

				.ulurl-error {
					padding: 12px;
					border-left: 4px solid #d63638;
					background: #fcf0f1;
					color: #8a2424;
				}

				.ulurl-login .ulurl-back {
					margin-top: 20px;
					text-align: center;
				}
			</style>
		</head>
		<body>
			<main class="ulurl-login">
				<h1>
					<?php esc_html_e( 'Log In', 'universal-login-url' ); ?>
				</h1>

				<?php if ( '' !== $error_message ) : ?>
					<div class="ulurl-error" role="alert">
						<?php echo esc_html( $error_message ); ?>
					</div>
				<?php endif; ?>

				<form method="post" action="<?php echo esc_url( $login_url ); ?>">
					<?php
					wp_nonce_field(
						'ulurl_login_action',
						'ulurl_login_nonce'
					);
					?>

					<input
						type="hidden"
						name="redirect_to"
						value="<?php echo esc_attr( $redirect_to ); ?>"
					>

					<label for="ulurl-user">
						<?php esc_html_e( 'Username or Email Address', 'universal-login-url' ); ?>
					</label>

					<input
						type="text"
						name="log"
						id="ulurl-user"
						value="<?php echo esc_attr( $username ); ?>"
						autocomplete="username"
						required
					>

					<label for="ulurl-password">
						<?php esc_html_e( 'Password', 'universal-login-url' ); ?>
					</label>

					<input
						type="password"
						name="pwd"
						id="ulurl-password"
						autocomplete="current-password"
						required
					>

					<div class="ulurl-remember">
						<label>
							<input
								type="checkbox"
								name="rememberme"
								value="forever"
							>
							<?php esc_html_e( 'Remember Me', 'universal-login-url' ); ?>
						</label>
					</div>

					<button type="submit">
						<?php esc_html_e( 'Log In', 'universal-login-url' ); ?>
					</button>
				</form>

				<p class="ulurl-back">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php esc_html_e( 'Back to website', 'universal-login-url' ); ?>
					</a>
				</p>
			</main>
		</body>
		</html>
		<?php
	}
}