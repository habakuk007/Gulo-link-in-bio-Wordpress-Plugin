# Gulo Link-in-Bio — Claude Instructions

## What This Project Is

A **WordPress plugin** that provides a link-in-bio page — a self-hosted alternative to Linktree.  
Users go to **Gulo Link-in-Bio** (top-level admin menu, position 81) to configure their profile, colors, and links.  
Both Administrators and Editors can access the settings page via the custom `guloli_manage_settings` capability.  
The result is displayed by creating a WordPress Page and selecting **Gulo Link-in-Bio** from the Template dropdown.

## File Map

```
gulo-link-in-bio.php                   Plugin header + bootstrap (constants, requires, hooks)
uninstall.php                     Runs on plugin deletion: removes options + capability
readme.txt                        WordPress.org directory listing (required for submission)
includes/class-guloli-plugin.php     Singleton boot class; activate/deactivate statics
includes/class-guloli-settings.php   Settings helper: get(), get_links(), sanitize callbacks
includes/class-guloli-admin.php      Admin menu, Settings API registration, page render
includes/class-guloli-frontend.php   Page template registration + lazy asset enqueueing
templates/page-gulo-simple-bio-links.php    Full HTML page (DOCTYPE → wp_footer); sets $GULOLI_Settings/$gulo_links
templates/display.php             Accessible link-in-bio HTML partial (profile + nav + footer)
assets/css/frontend.css           Link-in-bio CSS (custom properties for theming)
assets/css/admin.css              Admin settings page styles
assets/js/admin.js                Links repeater, WP media uploader, color pickers
tests/bootstrap.php               PHPUnit bootstrap for integration tests (needs WP env)
tests/unit/bootstrap.php          PHPUnit bootstrap for unit tests (Brain\Monkey, no WP needed)
tests/unit/Test_GULOLI_Settings.php  Unit tests for GULOLI_Settings (32 tests, Brain\Monkey)
tests/integration/class-test-guloli-plugin.php  Integration tests (WP_UnitTestCase)
phpcs.xml                         PHPCS / WPCS configuration
phpunit.xml.dist                  PHPUnit config — integration tests (tests/integration/)
phpunit.unit.xml                  PHPUnit config — unit tests (tests/unit/)
.wp-env.json                      wp-env config for Docker-based integration tests
composer.json                     PHP dev dependencies (WPCS, PHPUnit, PHPCompatibility)
package.json                      JS dev dependencies (ESLint, Stylelint)
.editorconfig                     Editor settings (tabs, UTF-8, LF)
.gitignore                        Ignored paths
.github/copilot-instructions.md   GitHub Copilot project overview
.github/instructions/             Copilot domain instruction files (WP, CSS, a11y, readme.txt)
.github/workflows/ci.yml          GitHub Actions: lint + test matrix
.vscode/settings.json             VS Code editor settings
.vscode/extensions.json           VS Code recommended extensions
```

## Two WordPress Options

| Option key     | Type          | Description                              |
|----------------|---------------|------------------------------------------|
| `GULOLI_Settings` | `array`       | Profile, colors, background              |
| `gulo_links`    | `string` JSON | Array of `{title, url, active}` objects  |

Settings keys in `GULOLI_Settings`:
`profile_name`, `profile_bio`, `profile_image`, `background_type`, `background_color`,
`gradient_start`, `gradient_end`, `button_style`, `button_bg_color`, `button_text_color`,
`profile_text_color`, `page_id`, `seo_noindex`

## Coding Rules

- **Prefix**: classes `GULO_`, options `gulo_`, JS globals `guloAdmin.*`
- **Text domain**: `gulo-link-in-bio` in every i18n call
- **Capability**: `guloli_manage_settings` for all permission checks in this plugin — never `manage_options`
- **Escape outputs**: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- **Sanitize inputs**: `sanitize_text_field()`, `sanitize_hex_color()`, `esc_url_raw()`
- **No inline JS/CSS** — CSS custom properties are injected via `wp_add_inline_style()` in `GULOLI_Frontend`
- **Nonce + capability** for any write action (Settings API handles this via `settings_fields()`)
- **Assets** — registered and enqueued lazily in `maybe_enqueue_assets()` (only on the template page)
- **Cache** — `GULOLI_Admin::purge_page_cache()` is called automatically on `update_option_GULOLI_Settings`; it clears WP object cache + 6 known caching plugins
- **PHP 7.4+** — avoid features that require PHP 8.x unless explicitly asked

## Accessibility Requirements

- Skip link (`#lib-links`) must remain the first focusable element in the template output
- `<h1>` for profile name, `<nav>` with `aria-label` for links, `<section>` for profile
- All link buttons must have `:focus-visible` styles with minimum 3:1 contrast
- External links (`target="_blank"`) must announce "(opens in new tab)" via `aria-label`
- `prefers-reduced-motion` must suppress `transform` animations
- Image `alt` must read "Profile photo of [Name]"

## Development Commands

```bash
# PHP dev dependencies
composer install

# Lint PHP (WPCS)
composer run lint:php

# Fix PHP style
composer run fix:php

# Run unit tests (Brain\Monkey — no WordPress environment needed)
composer run test:unit

# Run integration tests (needs WP_TESTS_DIR set up via bin/install-wp-tests.sh)
WP_TESTS_DIR=/tmp/wordpress-tests-lib composer run test

# JS/CSS dev dependencies
npm install

# Lint JS
npm run lint:js

# Lint CSS
npm run lint:css

# Re-extract translatable strings to POT (requires WP-CLI)
composer run make:pot

# Compile all PO files to MO binaries (requires WP-CLI)
composer run make:mo
```

## What NOT to Change Without Asking

- The option names `GULOLI_Settings` / `gulo_links` — changing them loses saved data
- `GULOLI_Frontend::TEMPLATE_KEY = 'gulo-simple-bio-links-template'` — changing it breaks pages already assigned the template
- The prefix `GULO_` / `gulo_` — collision-avoidance contract
- The `sanitize_*` callbacks in `GULOLI_Settings` — they protect data integrity
- The capability slug `guloli_manage_settings` — it is stored in the WordPress roles table; renaming it silently locks out Editors

## Instruction Files Referenced

Domain instructions from [awesome-copilot](https://awesome-copilot.github.com/instructions/) are
stored in `.github/instructions/` and apply automatically to GitHub Copilot. When working here,
follow the same rules:

- **WordPress**: `.github/instructions/wordpress.instructions.md`
- **Color/CSS**: `.github/instructions/html-css-style-color-guide.instructions.md`
- **Accessibility**: `.github/instructions/a11y.instructions.md`
- **readme.txt / changelog.txt**: `.github/instructions/readme-txt.instructions.md`
