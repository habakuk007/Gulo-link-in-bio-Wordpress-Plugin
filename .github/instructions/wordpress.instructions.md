---
applyTo: 'wp-content/plugins/**,wp-content/themes/**,**/*.php,**/*.inc,**/*.js,**/*.jsx,**/*.ts,**/*.tsx,**/*.css,**/*.scss,**/*.json'
description: 'Coding, security, and testing rules for WordPress plugins and themes'
---

# WordPress Development — Copilot Instructions

**Goal:** Generate WordPress code that is secure, performant, testable, and compliant with official WordPress practices. Prefer hooks, small functions, dependency injection (where sensible), and clear separation of concerns.

## 1) Core Principles
- Never modify WordPress core. Extend via **actions** and **filters**.
- For plugins, always include a header and guard direct execution in entry PHP files.
- Use unique prefixes or PHP namespaces to avoid global collisions.
- Enqueue assets; never inline raw `<script>`/`<style>` in PHP templates.
- Make user‑facing strings translatable and load the correct text domain.

### Plugin header — all available fields

*Source: [Plugin Header Requirements](https://developer.wordpress.org/plugins/the-basics/header-requirements/)*

- `Plugin Name:` *(required)* — displayed in the wp-admin Plugins list.
- `Plugin URI:` — unique URL for the plugin's home page; cannot be a WordPress.org URL.
- `Description:` — ≤ 140 characters; shown under the plugin name in wp-admin.
- `Version:` — use `version_compare()`-compatible format (e.g. `1.0.3`; note `1.02 > 1.1` in PHP).
- `Requires at least:` — minimum WordPress version (e.g. `6.0`).
- `Requires PHP:` — minimum PHP version (e.g. `7.4`).
- `Author:` — plugin author name(s).
- `Author URI:` — author's website.
- `License:` — short licence slug, e.g. `GPL-2.0-or-later`.
- `License URI:` — link to the full licence text.
- `Text Domain:` — must match the string in `load_plugin_textdomain()` and all i18n calls.
- `Domain Path:` — where translation files live, e.g. `/languages`.
- `Update URI:` — prevents accidental WP.org update hijacking for externally-distributed plugins.
- `Requires Plugins:` — comma-separated WP.org slugs of required plugins (WordPress 6.5+).
- `Network:` — set `true` only for network-wide plugins; omit otherwise.

```php
<?php
defined( 'ABSPATH' ) || exit;
/**
 * Plugin Name:       Awesome Feature
 * Plugin URI:        https://example.com/awesome-feature/
 * Description:       Example plugin scaffold.
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Example Author
 * Author URI:        https://example.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       awesome-feature
 * Domain Path:       /languages
 * Update URI:        https://example.com/awesome-feature/
 */
```

## 2) Coding Standards (PHP, JS, CSS, HTML)
- Follow **WordPress Coding Standards (WPCS)** and write DocBlocks for public APIs.
- PHP: Prefer strict comparisons (`===`, `!==`) where appropriate. Be consistent with array syntax and spacing as per WPCS.
- JS: Match WordPress JS style; prefer `@wordpress/*` packages for block/editor code.
- CSS: Use BEM‑like class naming when helpful; avoid over‑specific selectors.
- PHP 7.4+ compatible patterns unless the project specifies higher. Avoid using features not supported by target WP/PHP versions.

### Linting setup suggestions
```xml
<!-- phpcs.xml -->
<?xml version="1.0"?>
<ruleset name="Project WPCS">
  <description>WordPress Coding Standards for this project.</description>
  <file>./</file>
  <exclude-pattern>vendor/*</exclude-pattern>
  <exclude-pattern>node_modules/*</exclude-pattern>
  <rule ref="WordPress"/>
  <rule ref="WordPress-Docs"/>
  <rule ref="WordPress-Extra"/>
  <rule ref="PHPCompatibility"/>
  <config name="testVersion" value="7.4-"/>
</ruleset>
```

```json
// composer.json (snippet)
{
  "require-dev": {
    "dealerdirect/phpcodesniffer-composer-installer": "^1.0",
    "wp-coding-standards/wpcs": "^3.0",
    "phpcompatibility/php-compatibility": "^9.0"
  },
  "scripts": {
    "lint:php": "phpcs -p",
    "fix:php": "phpcbf -p"
  }
}
```

```json
// package.json (snippet)
{
  "devDependencies": {
    "@wordpress/eslint-plugin": "^x.y.z"
  },
  "scripts": {
    "lint:js": "eslint ."
  }
}
```

## 3) Security & Data Handling

*Official references: [Security](https://developer.wordpress.org/apis/security/) · [Escaping](https://developer.wordpress.org/apis/security/escaping/) · [Sanitizing](https://developer.wordpress.org/apis/security/sanitizing/) · [Nonces](https://developer.wordpress.org/apis/security/nonces/)*

- **Escape on output — escape late.** Escape at the point of output, not earlier. Escaping too early and then concatenating can silently double-escape data.
  - HTML element content: `esc_html()` · HTML attribute: `esc_attr()` · URL in `href`/`src`: `esc_url()`
  - URL stored in DB: `esc_url_raw()` · Inline JS value: `esc_js()` · `<textarea>` content: `esc_textarea()`
  - XML/XSL context: `esc_xml()` · Trusted post HTML: `wp_kses_post()` · Custom allowed HTML: `wp_kses( $html, $allowed_tags )`
  - Integers: `absint()` / `(int)`
  - Combined escape + translation (preferred over separate calls): `esc_html__()`, `esc_html_e()`, `esc_html_x()`, `esc_attr__()`, `esc_attr_e()`, `esc_attr_x()`
- **Sanitize on input.** Prefer validation (reject bad input) over sanitization where the expected format is known.
  - Generic single-line text: `sanitize_text_field()` · Multi-line: `sanitize_textarea_field()`
  - Email: `sanitize_email()` · URL: `sanitize_url()` / `esc_url_raw()` · Hex colour: `sanitize_hex_color()`
  - CSS class: `sanitize_html_class()` · Key/identifier: `sanitize_key()` · Integer: `absint()` / `intval()`
  - HTML from editors: `wp_kses_post()`
- **Nonces & capabilities** for forms, AJAX, REST:
  - Add nonces: `wp_nonce_field()` (forms), `wp_create_nonce()` (AJAX/JS).
  - Verify: `check_admin_referer()` (admin forms), `check_ajax_referer()` (AJAX), `wp_verify_nonce()` (other contexts).
  - **Nonces are not authentication or authorisation.** Always pair nonce verification with `current_user_can()`. A valid nonce alone must never grant access.
  - Default nonce lifetime is 24 h; adjust via the `nonce_life` filter if needed.
- **Database:** always use `$wpdb->prepare()` with `%s`/`%d`/`%f` placeholders; never concatenate untrusted input into SQL.
- **Uploads:** validate MIME type; use `wp_handle_upload()` or `media_handle_upload()`.

```php
// Escape late — at the point of output
echo '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
echo esc_html__( 'Save settings', 'my-plugin' );

// Nonce: admin form
wp_nonce_field( 'save-settings_' . $post_id );
check_admin_referer( 'save-settings_' . $post_id );

// Nonce: AJAX
wp_localize_script( 'my-js', 'myData', [ 'nonce' => wp_create_nonce( 'my-action' ) ] );
check_ajax_referer( 'my-action' );
```

## 4) Internationalization (i18n)
- Wrap user‑visible strings with translation functions using your text domain:
  - `__( 'Text', 'link-in-bio' )`, `_x()`, `esc_html__()`.
- Load translations with `load_plugin_textdomain()` or `load_theme_textdomain()`.
- Keep a `.pot` in `/languages` and ensure consistent domain usage.

## 5) Performance
- Defer heavy logic to specific hooks; avoid expensive work on `init`/`wp_loaded` unless necessary.
- Use transients or object caching for expensive queries; plan invalidation.
- Enqueue only what you need and conditionally (front vs admin; specific screens/routes).
- Prefer paginated/parameterized queries over unbounded loops.

## 6) Admin UI & Settings
- Use **Settings API** for options pages; provide `sanitize_callback` for each setting.
- For tables, follow `WP_List_Table` patterns. For notices, use the admin notices API.
- Avoid direct HTML echoing for complex UIs; prefer templates or small view helpers with escaping.

## 7) REST API
- Register with `register_rest_route()`; always set a `permission_callback`.
- Validate/sanitize request args via the `args` schema.
- Return `WP_REST_Response` or arrays/objects that map cleanly to JSON.

## 8) Blocks & Editor (Gutenberg)
- Use `block.json` + `register_block_type()`; rely on `@wordpress/*` packages.
- Provide server render callbacks when needed (dynamic blocks).
- E2E tests should cover: insert block → edit → save → front‑end render.

## 9) Asset Loading
```php
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style(
    'af-frontend',
    plugins_url('assets/frontend.css', __FILE__),
    [],
    '0.1.0'
  );

  wp_enqueue_script(
    'af-frontend',
    plugins_url('assets/frontend.js', __FILE__),
    [ 'wp-i18n', 'wp-element' ],
    '0.1.0',
    true
  );
});
```
- Use `wp_register_style/script` to register first if multiple components depend on the same assets.
- For admin screens, hook into `admin_enqueue_scripts` and check screen IDs.

## 10) Testing
### PHP Unit/Integration
- Use **WordPress test suite** with `PHPUnit` and `WP_UnitTestCase`.
- Test: sanitization, capability checks, REST permissions, DB queries, hooks.
- Prefer factories (`self::factory()->post->create()` etc.) to set up fixtures.

```xml
<!-- phpunit.xml.dist (minimal) -->
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="tests/bootstrap.php" colors="true">
  <testsuites>
    <testsuite name="Plugin Test Suite">
      <directory suffix="Test.php">tests/</directory>
    </testsuite>
  </testsuites>
</phpunit>
```

```php
// tests/bootstrap.php (minimal sketch)
<?php
$_tests_dir = getenv('WP_TESTS_DIR') ?: '/tmp/wordpress-tests-lib';
require_once $_tests_dir . '/includes/functions.php';
tests_add_filter( 'muplugins_loaded', function () {
  require dirname(__DIR__) . '/link-in-bio.php';
} );
require $_tests_dir . '/includes/bootstrap.php';
```
### E2E
- Use Playwright (or Puppeteer) for editor/front‑end flows.
- Cover basic user journeys and regressions (block insertion, settings save, front‑end render).

## 11) Documentation & Commits
- Keep `README.md` up to date: install, usage, capabilities, hooks/filters, and test instructions.
- Use clear, imperative commit messages; reference issues/tickets and summarize impact.

## 12) What Copilot Must Ensure (Checklist)
- ✅ Unique prefixes/namespaces; no accidental globals.  
- ✅ Nonce + capability checks for any write action (AJAX/REST/forms).  
- ✅ Inputs sanitized; outputs escaped.  
- ✅ User‑visible strings wrapped in i18n with correct text domain (`link-in-bio`).  
- ✅ Assets enqueued via APIs (no inline script/style).  
- ✅ Tests added/updated for new behaviors.  
- ✅ Code passes PHPCS (WPCS) and ESLint where applicable.  
- ✅ Avoid direct DB concatenation; always prepare queries.
- ✅ All bundled third-party libraries and assets are GPL-compatible.
- ✅ No obfuscated or minified-with-name-mangling code; build sources included or linked.
- ✅ No feature-gating or functionality restricted behind payment/upgrade.
- ✅ Any external HTTP request is gated behind explicit user opt-in.
- ✅ No third-party CDNs for non-font assets; use WP-bundled libraries (jQuery, etc.).
- ✅ No `<iframe>` elements on admin pages.
- ✅ Admin notices and dashboard widgets are dismissible.
- ✅ "Powered by" / credit links are opt-in and hidden by default.

## 13) WordPress.org Directory Compliance

### Licensing (Guideline 1)
All files distributed with the plugin — including bundled JavaScript libraries, images, fonts, and PHP dependencies — must use the GPL v2 or later, or a [GPL-compatible license](https://www.gnu.org/licenses/license-list.html). Verify the license of any third-party library before including it.

### Human-Readable Code (Guideline 4)
- Never obfuscate code or use minification that mangles variable/function names.
- If you use a build step (e.g. webpack, esbuild), include the unminified source files in the plugin, or link to a public development repository (e.g. GitHub) in `readme.txt`.
- Compiled/minified production assets are fine as long as the source is accessible.

### No Trialware (Guideline 5)
- Do not restrict or disable any plugin functionality that is then unlocked by payment.
- Trial periods that silently disable features are prohibited.
- All features visible in the plugin must work without purchasing an upgrade.

### External HTTP Requests & User Tracking (Guideline 7)
- Plugins must **not** contact external servers without explicit user consent (opt-in).
- Prohibited without opt-in: automated telemetry, analytics pings, remote asset loading for tracking, passing user data to third parties.
- If a feature requires external communication, expose an explicit settings toggle; default it to **off**.

```php
// Example: gate any outbound call behind a user-enabled option
if ( get_option( 'myplugin_enable_telemetry' ) ) {
    wp_remote_post( 'https://example.com/telemetry', [ 'body' => $data ] );
}
```

### External Assets & Bundled Libraries (Guidelines 8 + 13)
- **Use WordPress-bundled libraries** (jQuery, Backbone, Underscore, SimplePie, etc.) instead of bundling your own copy. Register them with the handle WordPress already defines.
- Do **not** load assets from third-party CDNs (jsDelivr, cdnjs, unpkg, etc.) except for web fonts. Self-host or use the WP-bundled version.
- Do **not** serve plugin updates or install additional plugins/themes from non-WordPress.org servers.
- Do **not** use `<iframe>` elements on admin/settings pages.

### Admin UI Conduct (Guideline 11)
- Upgrade prompts and upsell notices must be **contextual** (settings page only) and **minimal**.
- Sitewide admin notices must be **dismissible** — provide a nonce-protected dismiss action.
- Dashboard widgets added by the plugin must be removable via the standard Screen Options panel.
- Do not add advertising banners in the WordPress admin.

```php
// Dismissible notice pattern
add_action( 'admin_notices', 'myplugin_maybe_show_notice' );
function myplugin_maybe_show_notice() {
    if ( get_option( 'myplugin_notice_dismissed' ) ) {
        return;
    }
    echo '<div class="notice notice-info is-dismissible" id="myplugin-notice">';
    echo '<p>' . esc_html__( 'Notice text.', 'my-plugin' ) . '</p>';
    echo '</div>';
}
```

### Attribution & Credit Links (Guideline 10)
- Any "Powered by [Plugin Name]" or credit link rendered on the front end must be **opt-in** and **hidden by default**.
- Provide a clear, labeled toggle in settings; never require credits to unlock functionality.

### Developer Responsibility for All Bundled Assets (Guideline 2)
- You are responsible for every file you distribute — plugin code, images, fonts, and third-party libraries.
- Before adding any third-party library, verify its licence is GPL-compatible and document its source in comments or `readme.txt`.
- If a security issue is found in bundled code, patch it promptly or remove the component.

### SVN is a Release Repository Only (Guideline 3)
- Only push production-ready, deployable code to SVN. The directory generates a zip on every commit.
- Distributing plugin updates through channels other than WordPress.org while keeping SVN stale is prohibited.
- Tag each release with its version number; never use `trunk` as the `Stable tag`.

### External Services / SaaS (Guideline 6)
- Plugins that interface with external paid or free services are permitted.
- Always document the service in `readme.txt`: what it does, its pricing model, and a link to its Terms of Use.
- Never create an artificial external dependency solely to move code out of the plugin.

### No Illegal, Dishonest, or Manipulative Behaviour (Guideline 9)
- No keyword stuffing, black-hat SEO, or artificial search-ranking manipulation.
- No fake reviews, sockpuppeting (multiple accounts for reviews/ratings), or pressuring users for reviews.
- No implying legal-compliance guarantees ("GDPR-compliant", "ADA-compliant").
- No unauthorized use of the user's server resources (e.g. crypto mining, botnet participation).
- No copying another developer's plugin and presenting it as original work.

### Readme / Public Pages Must Not Spam (Guideline 12)
- Maximum **12 tags** in `readme.txt`; only the first **5** are displayed on WordPress.org (all 12 contribute to search).
- Tags may not include competitor brand names; related product tags are permitted (e.g. `woocommerce` for a WC extension).
- Repeating a tag or keyword counts as keyword stuffing and is prohibited.
- Affiliate links must be **disclosed** and must link **directly** to the affiliate service — no cloaked or redirect URLs.

### SVN Commit Discipline (Guideline 14)
- Commit only deployable code; every SVN commit regenerates the plugin zip file.
- Avoid rapid "cleanup" or "update" commits; use descriptive messages explaining what changed and why.
- Use a VCS (e.g. GitHub) for day-to-day development; push to SVN only when releasing.

### Increment Version for Every Code Release (Guideline 15)
- Users receive update prompts only when the version number increases. Every code change reaching users needs a new version.
- The trunk `readme.txt` `Stable tag` must always match the current deployed version.

### Respect Trademarks and Copyrights (Guideline 17)
- A plugin slug must **not** begin with another product's registered trademark (e.g. `wordpress`, `woocommerce`) unless you are the official owner.
- Choose original, unique plugin names; avoid confusingly similar names to established products.
- Forking another plugin is permitted under the GPL, but you must credit the original and comply with its licence.

*Source: [Detailed Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/) · [Plugin Developer FAQ](https://developer.wordpress.org/plugins/wordpress-org/plugin-developer-faq/)*
