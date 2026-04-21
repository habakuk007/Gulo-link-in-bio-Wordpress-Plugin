# Bio Links — WordPress Plugin

A link-in-bio page for WordPress — a self-hosted alternative to Linktree. Create a dedicated page,
select the **Bio Links** page template, and your profile page is live — no shortcodes, no page builders.

---

## Features

- **Profile section** — circular avatar, name, bio/tagline
- **Custom links** — unlimited links, drag-to-reorder, toggle active/inactive
- **Theming** — solid or gradient background, custom button & text colors
- **Accessible** — WCAG 2.2 AA compliant: skip link, semantic landmarks, visible focus, `prefers-reduced-motion`
- **i18n ready** — all strings wrapped in WordPress translation functions
- **Standalone page template** — bypasses the active theme, no page builders or extra plugins needed

---

## Installation

### From source

1. Clone or download this repository into your `wp-content/plugins/` directory:

   ```bash
   cd wp-content/plugins/
   git clone https://github.com/habakuk007/Wordpress-LinkInBio-Template.git link-in-bio
   ```

2. In WordPress admin, go to **Plugins → Installed Plugins** and activate **Bio Links**.

### As a ZIP

1. [Download the latest release](https://github.com/habakuk007/Wordpress-LinkInBio-Template/releases)
2. Go to **Plugins → Add New → Upload Plugin** and upload the ZIP.
3. Activate the plugin.

---

## Configuration

1. Go to **Bio Links** in the WordPress admin menu (top-level item, below Settings).
2. Fill in your **Profile** details (name, bio, avatar image).
3. Set your **Appearance** (background gradient or solid color, button and text colors).
4. Add your **Links** — title, URL, and active toggle. Drag rows to reorder.
5. Click **Save Settings**.

---

## Usage

1. Go to **Pages → Add New** and give the page a title (e.g. "Links").
2. In the **Page Attributes** panel (classic editor) or the **Template** dropdown in the sidebar
   (block editor), select **Bio Links**.
3. Publish the page.

The plugin serves a fully self-contained HTML page that bypasses your active theme entirely,
so your link-in-bio page looks the same regardless of which theme is installed.

---

## Access Control

The plugin registers a custom WordPress capability: `lib_manage_settings`.

| Role | Access |
|------|--------|
| Administrator | Full access |
| Editor | Full access |
| Author, Contributor, Subscriber | No access |

The capability is granted automatically when the plugin is activated. It persists in the
WordPress roles table — removing it requires deactivating the plugin (which removes it cleanly).

When an Administrator or Editor views the Bio Links page on the frontend, an
**Edit Bio Links** shortcut appears in the WordPress admin bar.

---

## Programmatic usage

Settings and links are stored as WordPress options and can be set via `update_option()`:

```php
// Override settings programmatically
update_option( 'lib_settings', array_merge(
    get_option( 'lib_settings', array() ),
    array( 'profile_name' => 'My Brand' )
) );
```

### Available settings keys (`lib_settings`)

| Key | Type | Description |
|-----|------|-------------|
| `profile_name` | string | Display name |
| `profile_bio` | string | Tagline or short bio |
| `profile_image` | string (URL) | Avatar image URL |
| `background_type` | `gradient` \| `solid` | Background style |
| `gradient_start` | hex color | Gradient top color |
| `gradient_end` | hex color | Gradient bottom color |
| `background_color` | hex color | Solid background color |
| `button_style` | `solid` \| `glass` | Button appearance |
| `button_bg_color` | hex color | Button background |
| `button_text_color` | hex color | Button label color |
| `profile_text_color` | hex color | Name and bio text color |
| `page_id` | int | WordPress page ID that shows the template |
| `seo_noindex` | `1` \| `''` | Whether to add `noindex` to the page |

### Links option (`lib_links`)

Stored as a JSON string. Each item: `{"title": "...", "url": "...", "active": true}`.

---

## Hooks & Filters

### Actions

| Hook | When | Use |
|------|------|-----|
| `update_option_lib_settings` | After settings save | Extend cache purging |

### Filters (Yoast SEO integration)

When Yoast SEO is active, the plugin adjusts its output via Yoast's own filters instead of
emitting competing HTML tags:

| Filter | Effect |
|--------|--------|
| `wpseo_title` | Sets page title to `{Profile Name} - {Site Name}` |
| `wpseo_opengraph_type` | Sets OG type to `profile` |
| `wpseo_opengraph_title` | Sets OG title to the profile name |
| `wpseo_robots` | Passes through `noindex` when the SEO option is set |

---

## Development

### Requirements

- PHP 7.4+
- Composer
- Node.js 18+
- npm

### Setup

```bash
# Clone
git clone https://github.com/habakuk007/Wordpress-LinkInBio-Template.git
cd wordpress-link-in-bio

# PHP dependencies (WPCS, PHPUnit, etc.)
composer install

# JS dependencies (ESLint, Stylelint)
npm install
```

### Lint & Fix

```bash
# PHP — check coding standards
composer run lint:php

# PHP — auto-fix style
composer run fix:php

# JS — lint
npm run lint:js

# CSS — lint
npm run lint:css
```

### Tests

The test suite is split into **unit tests** (fast, no WordPress needed) and **integration tests** (require a real WP environment).

#### Unit tests — run locally right now

```bash
composer run test:unit
```

Unit tests use [Brain\Monkey](https://brain-wp.github.io/BrainMonkey/) to stub WordPress functions. No database, no WP install required.

#### Integration tests — require a WordPress environment

**Option A — local MySQL:**

```bash
# One-time setup (adjust DB credentials)
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest

# Run suite
WP_TESTS_DIR=/tmp/wordpress-tests-lib composer run test
```

**Option B — Docker via wp-env:**

```bash
npx @wordpress/env start
composer run test
```

### Translations

The plugin ships with German (`de_DE`), French (`fr_FR`), Spanish (`es_ES`), and Ukrainian (`uk`)
translations. All strings are wrapped in WordPress i18n functions with the text domain `link-in-bio`.

To add or update translations:

```bash
# 1. Re-extract strings from source → languages/link-in-bio.pot
composer run make:pot

# 2. Edit the relevant .po file (e.g. in Poedit or a text editor)

# 3. Compile all .po files → .mo binaries
composer run make:mo
```

Both commands require [WP-CLI](https://wp-cli.org/) to be available as `wp` in `$PATH`.

---

## File Structure

```text
link-in-bio/
├── link-in-bio.php               ← Plugin entry point
├── uninstall.php                 ← Removes options and capability on plugin deletion
├── readme.txt                    ← WordPress.org directory listing
├── includes/
│   ├── class-lib-plugin.php      ← Bootstrap, lifecycle, capability grants
│   ├── class-lib-settings.php    ← Options helper & sanitizers
│   ├── class-lib-admin.php       ← Admin menu, settings page, cache purge
│   └── class-lib-frontend.php    ← Page template, assets, SEO meta, admin bar
├── templates/
│   ├── page-link-in-bio.php      ← Full HTML page (DOCTYPE → wp_footer)
│   └── display.php               ← Profile + links + footer partial
├── assets/
│   ├── css/frontend.css          ← Frontend styles
│   ├── css/admin.css             ← Admin styles
│   └── js/admin.js               ← Admin JS (links repeater, media, colors)
├── languages/
│   ├── link-in-bio.pot           ← Translation template
│   ├── link-in-bio-de_DE.po      ← German (source)
│   ├── link-in-bio-de_DE.mo      ← German (compiled)
│   ├── link-in-bio-fr_FR.po      ← French (source)
│   ├── link-in-bio-fr_FR.mo      ← French (compiled)
│   ├── link-in-bio-es_ES.po      ← Spanish (source)
│   ├── link-in-bio-es_ES.mo      ← Spanish (compiled)
│   ├── link-in-bio-uk.po         ← Ukrainian (source)
│   └── link-in-bio-uk.mo         ← Ukrainian (compiled)
├── tests/
│   ├── bootstrap.php                 ← Integration test bootstrap (WP env)
│   ├── unit/
│   │   ├── bootstrap.php             ← Unit test bootstrap (Brain\Monkey)
│   │   └── Test_LIB_Settings.php     ← Unit tests for LIB_Settings
│   └── integration/
│       └── class-test-lib-plugin.php ← Integration tests (WP_UnitTestCase)
├── .github/
│   ├── copilot-instructions.md
│   ├── instructions/             ← Copilot domain rules (WP, CSS, a11y)
│   └── workflows/ci.yml
├── .vscode/
│   ├── extensions.json
│   └── settings.json
├── CLAUDE.md
├── CONTRIBUTING.md
├── composer.json
├── package.json
├── phpcs.xml
├── phpunit.xml.dist              ← PHPUnit config (integration tests)
├── phpunit.unit.xml              ← PHPUnit config (unit tests)
├── .wp-env.json                  ← wp-env / Docker config
└── .editorconfig
```

---

## Troubleshooting

**The Bio Links page shows the theme instead of the plugin layout.**
Go to **Bio Links** in wp-admin, scroll to the **Bio Links Page** section, and select the
correct page. Alternatively confirm that the page's Template (Page Attributes) is set to
**Bio Links** in the block editor.

**Translations are not loading.**
Check that MO files exist in `languages/` (`link-in-bio-{locale}.mo`). If you added or changed
PO files, run `composer run make:mo` to recompile them. Ensure the WordPress site language
matches the locale (e.g., `de_DE`).

**The settings page is not visible for an Editor.**
The `lib_manage_settings` capability must be present in the `editor` role. Deactivate and
reactivate the plugin to re-grant it. Verify with:
`get_role('editor')->has_cap('lib_manage_settings')` in a PHP snippet.

**Changes to settings are not reflected on the frontend.**
A caching plugin may be serving a stale page. Saving settings normally triggers automatic cache
purging for WP Super Cache, WP Rocket, W3 Total Cache, WP Fastest Cache, LiteSpeed Cache, and
Cache Enabler. If your caching plugin is not listed, purge it manually or hook into
`update_option_lib_settings`.

**Yoast SEO shows wrong title or OG tags for the Bio Links page.**
Ensure you are running version 1.0.0-alpha.7 or later. The plugin hooks into Yoast's own filters
(`wpseo_title`, `wpseo_opengraph_type`, `wpseo_opengraph_title`, `wpseo_robots`) to avoid
duplicate meta tags.

---

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for development setup, coding standards, and the PR process.

---

## License

[GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html)
