# Link in Bio — Claude Instructions

## What This Project Is

A **WordPress plugin** that provides a Linktree-style profile link page.  
Users go to **Settings → Link in Bio** in wp-admin to configure their profile, colors, and links.  
The result is displayed by creating a WordPress Page and selecting **Link in Bio** from the Template dropdown.

## File Map

```
link-in-bio.php                   Plugin header + bootstrap (constants, requires, hooks)
includes/class-lib-plugin.php     Singleton boot class; activate/deactivate statics
includes/class-lib-settings.php   Settings helper: get(), get_links(), sanitize callbacks
includes/class-lib-admin.php      Admin menu, Settings API registration, page render
includes/class-lib-frontend.php   Page template registration + lazy asset enqueueing
templates/page-link-in-bio.php    Full HTML page (DOCTYPE → wp_footer); sets $lib_settings/$lib_links
templates/display.php             Accessible Linktree-style HTML partial (profile + nav + footer)
assets/css/frontend.css           Linktree-inspired CSS (custom properties for theming)
assets/css/admin.css              Admin settings page styles
assets/js/admin.js                Links repeater, WP media uploader, color pickers
tests/bootstrap.php               PHPUnit bootstrap (WP test suite)
tests/class-test-lib-settings.php Unit tests for LIB_Settings
phpcs.xml                         PHPCS / WPCS configuration
phpunit.xml.dist                  PHPUnit configuration
composer.json                     PHP dev dependencies (WPCS, PHPUnit, PHPCompatibility)
package.json                      JS dev dependencies (ESLint, Stylelint)
.editorconfig                     Editor settings (tabs, UTF-8, LF)
.gitignore                        Ignored paths
.github/copilot-instructions.md   GitHub Copilot project overview
.github/instructions/             Copilot domain instruction files (WP, CSS, a11y)
.github/workflows/ci.yml          GitHub Actions: lint + test matrix
.vscode/settings.json             VS Code editor settings
.vscode/extensions.json           VS Code recommended extensions
```

## Two WordPress Options

| Option key     | Type          | Description                              |
|----------------|---------------|------------------------------------------|
| `lib_settings` | `array`       | Profile, colors, background              |
| `lib_links`    | `string` JSON | Array of `{title, url, active}` objects  |

Settings keys in `lib_settings`:
`profile_name`, `profile_bio`, `profile_image`, `background_type`, `background_color`,
`gradient_start`, `gradient_end`, `button_style`, `button_bg_color`, `button_text_color`,
`profile_text_color`

## Coding Rules

- **Prefix**: classes `LIB_`, options `lib_`, JS globals `libAdmin.*`
- **Text domain**: `link-in-bio` in every i18n call
- **Escape outputs**: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- **Sanitize inputs**: `sanitize_text_field()`, `sanitize_hex_color()`, `esc_url_raw()`
- **No inline JS/CSS** — CSS custom properties are injected via `wp_add_inline_style()` in `LIB_Frontend`
- **Nonce + capability** for any write action (Settings API handles this via `settings_fields()`)
- **Assets** — registered and enqueued lazily in `maybe_enqueue_assets()` (only on the template page)
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

# Run tests (needs WP_TESTS_DIR)
WP_TESTS_DIR=/tmp/wordpress-tests-lib composer run test

# JS/CSS dev dependencies
npm install

# Lint JS
npm run lint:js

# Lint CSS
npm run lint:css
```

## What NOT to Change Without Asking

- The option names `lib_settings` / `lib_links` — changing them loses saved data
- `LIB_Frontend::TEMPLATE_KEY = 'link-in-bio-template'` — changing it breaks pages already assigned the template
- The prefix `LIB_` / `lib_` — collision-avoidance contract
- The `sanitize_*` callbacks in `LIB_Settings` — they protect data integrity

## Instruction Files Referenced

Domain instructions from [awesome-copilot](https://awesome-copilot.github.com/instructions/) are
stored in `.github/instructions/` and apply automatically to GitHub Copilot. When working here,
follow the same rules:

- **WordPress**: `.github/instructions/wordpress.instructions.md`
- **Color/CSS**: `.github/instructions/html-css-style-color-guide.instructions.md`
- **Accessibility**: `.github/instructions/a11y.instructions.md`
