# WordPress Plugin Security, Licensing, and Quality Guidelines

This document captures security, compatibility, licensing, and coding standards guidance for the Floaty Button plugin. It is adapted from the WordPress.org plugin handbook and related best practices, and can be shared with automated reviewers or contributors.

## 1. Safety & Compatibility Goals

**Target environment**

- **WordPress version:** Current stable WordPress (6.6+) with compatibility for at least the last two major versions.
- **PHP version:**
  - WordPress requires PHP 7.4+ and supports up to PHP 8.3.
  - Use PHP 8.0+ as the development baseline; avoid features newer than the minimum supported version.

**Security goal**

> **Security goal:** This plugin aims to comply with WordPress.org’s plugin guidelines and the WordPress Plugin Security Handbook, prioritizing least privilege, full input validation/sanitization, and secure use of the WordPress APIs.

## 2. Licensing & WordPress.org Acceptance

### 2.1 License choice

- Use **GPLv2 or later** (same as WordPress core) for the plugin and any bundled assets.

### 2.2 Plugin main file header

Include at least the following in the main plugin file header:

- `Plugin Name`
- `Description`
- `Version`
- `Author`
- `Text Domain`
- `License`
- `License URI`

Example:

```php
<?php
/**
 * Plugin Name: My Safe Plugin
 * Plugin URI:  https://example.com/my-safe-plugin
 * Description: Example plugin following WordPress.org security and licensing guidelines.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * Text Domain: my-safe-plugin
 * Requires at least: 6.4
 * Tested up to:      6.6
 * Requires PHP:      8.0
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
```

### 2.3 README.txt licensing block

For a WordPress.org-friendly `readme.txt`, include:

```
Requires at least: 6.4
Tested up to: 6.6
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
```

### 2.4 Third-party code & assets

- Ensure all libraries, scripts, styles, fonts, and images are GPL-compatible.
- Keep a `licenses/` directory with a main `LICENSE` file and one file per third-party library.
- Avoid closed-source SDKs, obfuscated/encrypted PHP, and non-human-readable code.

### 2.5 Tracking & privacy rules

- No tracking without explicit consent and a clear opt-in/out control.
- Document any data collection in the plugin description, UI, and privacy policy (if applicable).

## 3. Secure Plugin Implementation Checklist

### 3.1 Structure & bootstrap

- Block direct access to PHP files: `if ( ! defined( 'ABSPATH' ) ) { exit; }`.
- Use one main bootstrap file and load other classes from `includes/` via autoloading or Composer.
- Namespace or prefix classes, functions, globals, and constants.

### 3.2 Capabilities & authorization

- Use `current_user_can()` before sensitive actions (options changes, exports, admin endpoints).
- Prefer specific capabilities or custom caps via `map_meta_cap()` and `register_post_type()`.
- Do not rely on `is_admin()` for authorization.

### 3.3 Nonces & CSRF protection

- Add nonces for all state-changing requests (forms, AJAX, REST) using `wp_nonce_field()` or `wp_create_nonce()`.
- Validate nonces with `check_admin_referer()` or `check_ajax_referer()`.

### 3.4 Validation, sanitization & escaping

- Validate and sanitize all input: `sanitize_text_field()`, `sanitize_email()`, `esc_url_raw()`, `absint()`, explicit boolean casts, and strict enums.
- Escape all output: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`.

### 3.5 Database access & storage

- Prefer WordPress APIs (Options, Posts, Metadata) over raw SQL.
- Use `$wpdb->prepare()` for all custom queries; never concatenate untrusted data.
- Avoid storing secrets in plain text; favor hashing and expiring tokens.

### 3.6 AJAX, REST API & endpoints

- Register AJAX hooks with appropriate prefixes and capability checks; always verify nonces and sanitize input.
- For REST routes, supply a strict `permission_callback` and define `validate_callback`/`sanitize_callback` per argument.

### 3.7 File uploads & downloads

- Use `wp_handle_upload()`/`media_handle_upload()` with MIME whitelists, size limits, and capability checks.
- Never allow arbitrary paths for downloads; map IDs to known paths.

### 3.8 Output, admin pages & templates

- Build admin pages with WordPress APIs (`add_menu_page()`, `add_submenu_page()`).
- Escape all rendered values; do not include arbitrary paths from user input.
- Use template loaders (`locate_template()`) when overriding front-end templates.

### 3.9 Logging & error handling

- Do not expose stack traces or SQL errors to users.
- Use `error_log()`/`WP_DEBUG_LOG` for debugging; avoid `var_dump()`/`die()` in production.

### 3.10 Dependencies & update safety

- If using Composer, lock versions and avoid abandoned packages.
- Avoid `eval()`, `create_function()`, dynamic includes from user input, and remote code execution or self-updaters.

## 4. Coding Standards & Quality

- Follow WordPress Coding Standards (WPCS) for PHP/JS/CSS.
- Keep consistent prefixes/namespaces (e.g., `Floaty_Button_` or `Floaty\Button`).
- Factor large files into focused classes (Admin, Frontend, REST, etc.).
- Document complex functions and hooks with PHPDoc including `@since`, `@param`, `@return`.

### PHPCS + WPCS setup (recommended)

Example `phpcs.xml.dist`:

```xml
<?xml version="1.0"?>
<ruleset name="Floaty Button">
    <description>PHPCS rules for Floaty Button.</description>
    <rule ref="WordPress"/>
    <rule ref="WordPress-Extra"/>
    <rule ref="WordPress-Docs"/>
    <rule ref="WordPress-DB"/>
    <file>./</file>
    <exclude-pattern>vendor/</exclude-pattern>
</ruleset>
```

### Internationalization (i18n)

- Wrap all user-facing strings in translation functions with the `floaty-button-main` text domain.
- Load the text domain on init using `load_plugin_textdomain()`.
- Escape translated output as usual (`esc_html__()`, `esc_attr__()`).

## 5. Example Licensing Block (LICENSE file)

```
My Safe Plugin - A WordPress plugin.

Copyright (C) 2025 Your Name (https://example.com)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty
of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see https://www.gnu.org/licenses/gpl-2.0.html
```

## 6. Ready-to-Use Prompts for Code Review & Task Generation

### 6.1 Global context prompt

> **Role:** You are an expert WordPress plugin security and quality auditor.
> **Context:** You are reviewing a WordPress plugin intended for distribution on WordPress.org and for use on modern WordPress (6.6+) and PHP 8.0+ environments.
>
> **Hard requirements (must check and enforce):**
>
> 1. **Licensing & WordPress.org compliance**
>    - Plugin main file contains a valid header with `Plugin Name`, `Description`, `Version`, `Author`, `Text Domain`, `License`, and `License URI`.
>    - License is **GPLv2 or later** (or another GPL-compatible license) and clearly declared in both the plugin header and `readme.txt`.
>    - All included libraries, images, fonts, and other assets have GPL-compatible licenses and are documented in a `licenses/` directory or equivalent.
>    - The plugin does not contain obfuscated or encrypted code and is human-readable.
>    - No trialware or locked-down features that violate WordPress.org plugin guidelines.
>    - No tracking or telemetry without clear, explicit user consent and an opt-out mechanism.
> 2. **Security**
>    - Every state-changing request (forms, AJAX, REST) uses **nonces** and validates them.
>    - All privileged actions are protected with capability checks using `current_user_can()`.
>    - All external input is **validated and sanitized** using appropriate WordPress functions.
>    - All output that may include user-controlled data is properly **escaped**.
>    - All database queries using `$wpdb` are **prepared statements**.
>    - File uploads go through WordPress APIs with MIME checks and size limits.
>    - No use of `eval`, dynamic includes from user input, or remote code loading mechanisms.
> 3. **Coding standards & quality**
>    - PHP code follows **WordPress Coding Standards (WPCS)**.
>    - Functions, classes, hooks, and globals are properly **namespaced or prefixed**.
>    - Complex logic is factored into smaller units with clear responsibilities.
>    - User-facing strings are fully **internationalized** with the correct text domain.
>    - No debug output (`var_dump`, `print_r`, `die`, `exit`) left in production code.
>
> **Output style:**
> - Provide a summary of compliance and a list of issues with severity, category, file/line, description, and a concrete fix suggestion.
> - Include a developer task list with actionable steps.

### 6.2 Review the whole plugin prompt

Use when a full audit is needed:

> Read the entire WordPress plugin repository. Using the global guidelines, perform a full audit of licensing, WordPress.org compliance, security, and coding standards. List issues with file paths and line numbers plus fix recommendations, then provide a prioritized task list with title, description, severity, and impact.

### 6.3 Create improvement tasks from a diff prompt

Use for incremental reviews:

> Analyze the supplied git diff for new or modified code that might violate licensing, security, or coding standards. Suggest improvements and return actionable tasks as JSON with id, title, description, severity, category, files, and line ranges. Only include tasks that are actionable and meaningful for maintainability or compliance.

---

If you want to automate checks, consider adding PHPCS with WordPress Coding Standards, PHPStan, and CI workflows to enforce these rules on pull requests.
