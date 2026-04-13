# GitHub Copilot — Project Instructions

## Project Overview

**Link in Bio** is a WordPress plugin that provides a Linktree-style profile link page.
Users configure a profile (name, bio, avatar), appearance (colors, gradients), and a list of
links via **Settings → Link in Bio**. The page is embedded with the `[link_in_bio]` shortcode.

## Instruction Files

Additional domain-specific instructions are in `.github/instructions/`:

| File | Applies to | Purpose |
|------|-----------|---------|
| `wordpress.instructions.md` | `**/*.php`, `**/*.js`, `**/*.css` | WordPress coding standards, security, i18n |
| `html-css-style-color-guide.instructions.md` | `**/*.css`, `**/*.html` | Color palette rules, 60-30-10 rule, gradients |
| `a11y.instructions.md` | `**` | WCAG 2.2 AA accessibility, ARIA, skip links |

## Plugin Architecture

```
link-in-bio.php              ← Plugin entry point (header + bootstrap)
includes/
  class-lib-plugin.php       ← Singleton boot class, activation/deactivation
  class-lib-settings.php     ← Static helper: get(), get_links(), sanitize callbacks
  class-lib-admin.php        ← Admin menu, Settings API, settings page HTML
  class-lib-frontend.php     ← [link_in_bio] shortcode, asset registration
templates/
  display.php                ← Accessible HTML template (rendered by shortcode)
assets/
  css/frontend.css           ← Linktree-style frontend design (CSS custom props)
  css/admin.css              ← Admin settings page styles
  js/admin.js                ← Links repeater, media uploader, color pickers
languages/
  link-in-bio.pot            ← Translation template
tests/
  bootstrap.php              ← PHPUnit bootstrap (WP test suite)
  class-test-lib-settings.php ← Unit tests for LIB_Settings
```

## Key Conventions

- **Prefix**: All PHP classes use `LIB_` prefix; options use `lib_` prefix.
- **Text domain**: `link-in-bio` — always use this in i18n functions.
- **Settings API**: Two options — `lib_settings` (array) and `lib_links` (JSON string).
- **Escape on output**: Use `esc_html()`, `esc_attr()`, `esc_url()` everywhere.
- **Sanitize on input**: Use `sanitize_text_field()`, `sanitize_hex_color()`, `esc_url_raw()`.
- **No inline scripts/styles** except the CSS custom property block in `templates/display.php`
  (which uses pre-escaped values from PHP).
- **Assets**: Register with `wp_register_*`, enqueue lazily (only when shortcode is used).
- **Accessibility**: Skip link, semantic landmarks, WCAG 2.2 AA contrast, `prefers-reduced-motion`.

## Development Commands

```bash
# Install PHP dev dependencies
composer install

# Lint PHP (WPCS)
composer run lint:php

# Auto-fix PHP style
composer run fix:php

# Run PHPUnit (requires WP test suite at $WP_TESTS_DIR)
composer run test

# Install JS dev dependencies
npm install

# Lint JS
npm run lint:js

# Lint CSS
npm run lint:css
```

## When Adding Features

1. Put business logic in a class under `includes/`.
2. Register hooks in the constructor, not at file level.
3. Add sanitize callbacks for any new option.
4. Wrap user-visible strings in `esc_html__( 'Text', 'link-in-bio' )`.
5. Write or update tests in `tests/`.
6. Check WCAG 2.2 AA for any UI additions.
