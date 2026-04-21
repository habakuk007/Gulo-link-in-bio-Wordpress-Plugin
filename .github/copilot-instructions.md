# GitHub Copilot — Project Instructions

## Project Overview

**Simple Bio Links** is a WordPress plugin that provides a link-in-bio page — a self-hosted alternative to Linktree.
Users configure a profile (name, bio, avatar), appearance (colors, gradients), and a list of
links via the **Simple Bio Links** menu in wp-admin (top-level menu, accessible to Administrators
and Editors). The profile is displayed by creating a WordPress Page and assigning the
**Simple Bio Links** page template — no shortcodes, no page builders required.

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
  class-lib-plugin.php       ← Singleton boot class, activation/deactivation, capability grants
  class-lib-settings.php     ← Static helper: get(), get_links(), sanitize callbacks
  class-lib-admin.php        ← Top-level admin menu, Settings API, settings page HTML, cache purge
  class-lib-frontend.php     ← Page template registration, lazy asset enqueue, SEO meta, admin bar
templates/
  page-link-in-bio.php       ← Full standalone HTML page (DOCTYPE → wp_footer)
  display.php                ← Accessible profile + links + footer partial
assets/
  css/frontend.css           ← Link-in-bio page frontend design (CSS custom props)
  css/admin.css              ← Admin settings page styles
  js/admin.js                ← Links repeater, media uploader, color pickers
languages/
  link-in-bio.pot            ← Translation template (regenerate with: composer run make:pot)
  link-in-bio-de_DE.po/.mo   ← German
  link-in-bio-fr_FR.po/.mo   ← French
  link-in-bio-es_ES.po/.mo   ← Spanish
  link-in-bio-uk.po/.mo      ← Ukrainian
tests/
  bootstrap.php              ← PHPUnit bootstrap (WP test suite)
  class-test-lib-settings.php ← Unit tests for LIB_Settings
```

## Key Conventions

- **Prefix**: All PHP classes use `LIB_` prefix; options use `lib_` prefix.
- **Text domain**: `simple-bio-links` — always use this in i18n functions.
- **Settings API**: Two options — `lib_settings` (array) and `lib_links` (JSON string).
- **Capability**: `lib_manage_settings` — custom cap granted to Administrators and Editors on activation. Use this (not `manage_options`) for all capability checks in this plugin.
- **Escape on output**: Use `esc_html()`, `esc_attr()`, `esc_url()` everywhere.
- **Sanitize on input**: Use `sanitize_text_field()`, `sanitize_hex_color()`, `esc_url_raw()`.
- **No inline scripts/styles** except the CSS custom property block injected via `wp_add_inline_style()` in `LIB_Frontend`.
- **Assets**: Register with `wp_register_*`, enqueue lazily in `maybe_enqueue_assets()` (only when the Simple Bio Links page template is active).
- **Cache**: Saving settings triggers `purge_page_cache()` in `LIB_Admin`, which clears WP object cache and known caching plugins (WP Super Cache, WP Rocket, W3 Total Cache, WP Fastest Cache, LiteSpeed Cache, Cache Enabler).
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

# Re-extract translatable strings → languages/link-in-bio.pot
composer run make:pot

# Compile all PO files → MO binaries
composer run make:mo
```

## When Adding Features

1. Put business logic in a class under `includes/`.
2. Register hooks in the constructor, not at file level.
3. Add sanitize callbacks for any new option.
4. Wrap user-visible strings in `esc_html__( 'Text', 'link-in-bio' )`.
5. Write or update tests in `tests/`.
6. Check WCAG 2.2 AA for any UI additions.
7. Use `lib_manage_settings` (not `manage_options`) for any new capability check.
8. After adding new strings, run `composer run make:pot` and update the PO files.
