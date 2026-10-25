
=== Universal Login URL ===
Contributors: universal-login-url
Tags: login, security, custom-login, access-control, authentication
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Configure a custom WordPress login URL using a personalized login slug.

== Description ==

Universal Login URL allows website administrators to configure a personalized login URL for their WordPress website.

The plugin provides a dedicated top-level page in the WordPress admin menu where administrators can configure a custom login slug. When a valid slug is saved, the plugin registers a custom login route and displays a login form that uses WordPress authentication.

The plugin is designed to simplify login URL configuration while preserving WordPress authentication and supported account recovery actions.

Universal Login URL does not replace WordPress authentication or guarantee protection against unauthorized access. It is intended to complement, not replace, established website security practices.

= Features =

* Configure a personalized login slug.
* Validate and sanitize the configured slug.
* Access a custom login form through the configured route.
* Authenticate users through WordPress.
* Support the Remember Me option.
* Display login errors.
* Handle login redirects safely.
* Preserve supported standard login actions, including password recovery and password reset.
* Refresh rewrite rules when the configured slug changes.
* Restore standard WordPress login behavior when the custom login slug is empty.
* Display administrative information about the configured login URL.

= Security Information =

A custom login URL can make the login endpoint less obvious, but it is not a complete security solution.

The plugin does not guarantee protection against unauthorized access, brute-force attacks, or other security threats.

For better website security:

* Use strong, unique passwords.
* Keep WordPress, themes, and plugins updated.
* Maintain regular website backups.
* Consider two-factor authentication.
* Consider appropriate login rate limiting.
* Test plugin changes on a staging or development website before applying them to production.

Keep a secure record of your configured login URL before logging out of your administrator account.

== Installation ==

1. Back up your WordPress website before installing or updating the plugin.
2. Upload the plugin ZIP file through Plugins > Add New Plugin > Upload Plugin, or use your usual WordPress deployment process.
3. Activate Universal Login URL.
4. Navigate to Universal Login URL in the main WordPress admin menu.
5. Enter a custom login slug using lowercase letters, numbers, and hyphens.
6. Save your settings.
7. Open the configured login URL in a new browser tab.
8. Test login, logout, password recovery, password reset, and redirects before using the plugin on a production website.

== Frequently Asked Questions ==

= How do I configure my custom login URL? =

Go to Universal Login URL in the main WordPress admin menu, enter your preferred slug, and save the settings.

For example, if your website is https://example.com and your configured slug is custom-login, your custom login URL will be:

https://example.com/custom-login/

Replace example.com with your own website domain.

= What happens if the login slug field is empty? =

When the slug field is empty and the settings are saved, the custom login route is disabled. Standard WordPress login behavior is restored, allowing the usual WordPress login URLs to be used.

This allows you to stop using a custom login URL without uninstalling the plugin.

= What happens when I change the slug? =

After you save a valid new slug, the plugin updates its rewrite configuration so the new custom login route can be used without requiring you to manually visit Settings > Permalinks.

Always verify the new URL before logging out of your administrator account.

= Does the plugin replace WordPress authentication? =

No. The plugin uses WordPress authentication to validate login credentials. It does not introduce a separate user account system.

= Does the plugin support password recovery? =

The plugin preserves supported standard login actions, including password recovery and password reset.

Test these workflows on your own WordPress installation to verify compatibility with your hosting environment and other plugins.

= What should I do if I forget my custom login URL? =

If you can still access the WordPress administration area, navigate to Universal Login URL in the main WordPress admin menu and review the configured slug. You can also clear the slug field and save the settings to restore standard WordPress login behavior.

If you cannot access the administration area, use your hosting provider's recovery assistance or ask a qualified administrator to temporarily disable the plugin.

Before reactivating the plugin, verify that the custom slug setting has been cleared if necessary.

Keep a secure record of your custom login URL before logging out.

= What happens if I deactivate the plugin? =

Deactivating the plugin disables its custom login functionality. Standard WordPress login behavior should be verified after deactivation.

If you cannot access your website, use your hosting provider's recovery assistance or a qualified administrator to regain access.

Always test activation and deactivation on a staging or development website before performing these operations on a production website.

= Does a custom login URL guarantee website security? =

No. A custom URL only makes the login endpoint less obvious. It does not replace strong passwords, software updates, backups, two-factor authentication, or other appropriate security controls.

= What should I test before using the plugin on a live website? =

Test the custom login route, valid and invalid credentials, Remember Me, logout, password recovery, password reset, redirects, slug changes, an empty slug, plugin activation and deactivation, and compatibility with your WordPress version, PHP version, theme, and other plugins.

Perform these tests on a staging or development environment before deploying changes to a production website.

== Changelog ==

= 1.0.0 =
* Added a dedicated top-level WordPress admin menu page for configuring the custom login slug.
* Added custom login routing and a WordPress authentication form.
* Added Remember Me support and login error handling.
* Added safe login redirects and support for standard password recovery and reset actions.
* Added automatic rewrite-rule refresh when the configured slug changes.
* Added support for leaving the slug empty to restore standard WordPress login behavior.
* Added administrative information and security reminders for the configured login URL.
