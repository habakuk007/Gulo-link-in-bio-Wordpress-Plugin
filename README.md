# Link in Bio — WordPress Plugin

A Linktree-style profile link page for WordPress. Create a dedicated page, select the
**Link in Bio** page template, and your profile page is live — no shortcodes, no page builders.

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
   git clone https://github.com/stefanwagner/wordpress-link-in-bio.git link-in-bio
   ```

2. In WordPress admin, go to **Plugins → Installed Plugins** and activate **Link in Bio**.

### As a ZIP

1. [Download the latest release](https://github.com/stefanwagner/wordpress-link-in-bio/releases)
2. Go to **Plugins → Add New → Upload Plugin** and upload the ZIP.
3. Activate the plugin.

---

## Configuration

1. Go to **Settings → Link in Bio** in the WordPress admin.
2. Fill in your **Profile** details (name, bio, avatar image).
3. Set your **Appearance** (background gradient or solid color, button and text colors).
4. Add your **Links** — title, URL, and active toggle. Drag rows to reorder.
5. Click **Save Settings**.

---

## Usage

1. Go to **Pages → Add New** and give the page a title (e.g. "Links").
2. In the **Page Attributes** panel (classic editor) or the **Template** dropdown in the sidebar
   (block editor), select **Link in Bio**.
3. Publish the page.

The plugin serves a fully self-contained HTML page that bypasses your active theme entirely,
so the Linktree-style layout looks the same regardless of which theme is installed.

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
git clone https://github.com/stefanwagner/wordpress-link-in-bio.git
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

PHPUnit integration tests require the WordPress test suite:

```bash
# One-time setup (adjust DB credentials)
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest

# Run tests
WP_TESTS_DIR=/tmp/wordpress-tests-lib composer run test
```

---

## File Structure

```text
link-in-bio/
├── link-in-bio.php               ← Plugin entry point
├── includes/
│   ├── class-lib-plugin.php      ← Bootstrap & lifecycle
│   ├── class-lib-settings.php    ← Options helper & sanitizers
│   ├── class-lib-admin.php       ← Admin settings page
│   └── class-lib-frontend.php    ← Page template registration & assets
├── templates/
│   ├── page-link-in-bio.php      ← Full HTML page (DOCTYPE → wp_footer)
│   └── display.php               ← Profile + links + footer partial
├── assets/
│   ├── css/frontend.css          ← Frontend styles
│   ├── css/admin.css             ← Admin styles
│   └── js/admin.js               ← Admin JS (links repeater, media, colors)
├── languages/
│   └── link-in-bio.pot           ← Translation template
├── tests/
│   ├── bootstrap.php
│   └── class-test-lib-settings.php
├── .github/
│   ├── copilot-instructions.md
│   ├── instructions/             ← Copilot domain rules (WP, CSS, a11y)
│   └── workflows/ci.yml
├── .vscode/
│   ├── extensions.json
│   └── settings.json
├── CLAUDE.md
├── composer.json
├── package.json
├── phpcs.xml
├── phpunit.xml.dist
└── .editorconfig
```

---

## License

[GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html)
