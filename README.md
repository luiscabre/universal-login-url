# Universal Login URL

A WordPress plugin that allows administrators to customize the login URL of their WordPress website.

## Features

- **Custom Login URL:** Configure a personalized login slug instead of using the default login URL.
- **Login Protection:** Redirect unauthenticated requests to `wp-admin` and prevent direct access through `wp-login.php`.
- **WordPress Authentication:** Preserve standard WordPress login functionality, including Remember Me and login error messages.
- **Password Recovery:** Support password recovery and password reset workflows.
- **Automatic Rewrite Refresh:** Refresh rewrite rules when the custom login slug is updated.
- **Administration Settings:** Manage the plugin through its dedicated WordPress dashboard menu.

## Requirements

- WordPress
- PHP 8.2 or later

## Installation

1. Download the plugin ZIP file from the project's Releases page.
2. In WordPress, navigate to **Plugins → Add New Plugin → Upload Plugin**.
3. Upload the ZIP file and click **Install Now**.
4. Activate the plugin.
5. Open the **Universal Login URL** menu in the WordPress dashboard.
6. Configure your custom login slug and save the settings.
7. Save your custom login URL securely.

## Important Notes

- Keep your custom login URL in a safe place.
- Test the login and password recovery workflows after changing the slug.
- This plugin changes the login access route; it does not replace other WordPress security measures.

## Development

This repository contains the plugin source code.

## License

See the plugin's `readme.txt` and source files for licensing information.