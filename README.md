# Link in Bio — WordPress Plugin

A Linktree-style profile link page for WordPress. Embed a beautiful, accessible "link in bio" page
anywhere on your site using the `[link_in_bio]` shortcode.

---

## Features

- **Profile section** — circular avatar, name, bio/tagline
- **Custom links** — unlimited links, drag-to-reorder, toggle active/inactive
- **Theming** — solid or gradient background, custom button & text colors
- **Accessible** — WCAG 2.2 AA compliant: skip link, semantic landmarks, visible focus, `prefers-reduced-motion`
- **i18n ready** — all strings wrapped in WordPress translation functions
- **No page builders needed** — one shortcode, works in any theme

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

Add the shortcode to any page, post, or widget:

```
[link_in_bio]
```

### Full-page layout (recommended)

For the best Linktree look, create a dedicated WordPress page and set its template to **Full Width**
(if your theme supports it) or use a theme/plugin that allows hiding the header/footer.

---

## Filters & Hooks

| Hook | Type | Description |
|------|------|-------------|
| `lib_settings` | filter | Modify settings before they are passed to the template |
| `lib_links` | filter | Modify the links array before rendering |
| `lib_custom_css` | filter | Modify the inline CSS custom-property string |

Example — add a custom link programmatically:

```php
add_filter( 'lib_links', function ( array $links ): array {
    $links[] = [
        'title'  => 'My Custom Link',
        'url'    => 'https://example.com',
        'active' => true,
    ];
    return $links;
} );
```

> **Note:** The hooks above are planned for a future version. Currently settings are read directly
> from WordPress options. The `lib_settings` and `lib_links` options can be set programmatically
> via `update_option()` if needed.

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

```
link-in-bio/
├── link-in-bio.php               ← Plugin entry point
├── includes/
│   ├── class-lib-plugin.php      ← Bootstrap & lifecycle
│   ├── class-lib-settings.php    ← Options helper & sanitizers
│   ├── class-lib-admin.php       ← Admin settings page
│   └── class-lib-frontend.php    ← Shortcode & assets
├── templates/
│   └── display.php               ← Frontend HTML template
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
