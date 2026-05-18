=== Gulo Link-in-Bio ===
Contributors: habakuk
Tags: link in bio, linktree, links, profile, social links
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 0.2.0
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://trumpkin.de/donate

A link-in-bio page for WordPress — a self-hosted Linktree alternative. Assign the template to any Page. No shortcodes, no page builders.

== Description ==

**Gulo Link-in-Bio** turns a WordPress Page into a fully self-contained link-in-bio page — without touching your active theme. It is a self-hosted alternative to Linktree. Ideal for social media bios, creator profiles, and landing pages that collect all your important links in one place.

= How it works =

1. Install and activate the plugin.
2. Go to **Gulo Link-in-Bio** in the admin menu and configure your profile.
3. Create any WordPress Page, select it in the settings, then publish it.

The plugin serves a standalone HTML page (bypasses the active theme entirely), so your link-in-bio page looks identical no matter which theme is installed.

= Features =

* **Profile section** — circular avatar, name, and bio/tagline
* **Unlimited links** — drag to reorder, toggle active/inactive without deleting
* **Theming** — gradient or solid background, custom button colors (solid or glass/frosted style), custom text color
* **SEO options** — optional noindex to keep the page out of search results
* **Legal footer** — optional Imprint and Privacy Policy links (useful for GDPR / German Impressum requirements)
* **Yoast SEO compatible** — integrates via Yoast's own filters to avoid duplicate meta tags
* **Cache aware** — automatically purges the page cache on save (WP Super Cache, WP Rocket, W3 Total Cache, WP Fastest Cache, LiteSpeed Cache, Cache Enabler)
* **Editor access** — Administrators and Editors can manage the settings via a custom capability
* **Admin bar shortcut** — logged-in users with access see an "Edit Gulo Link-in-Bio" link directly on the frontend page
* **Fully accessible** — WCAG 2.2 AA: skip link, semantic landmarks, visible focus, `prefers-reduced-motion`
* **Translated** — ships with German (`de_DE`), French (`fr_FR`), Spanish (`es_ES`), and Ukrainian (`uk`) translations
* **No tracking, no ads, no upsells** — 100% free and open source (GPL-2.0-or-later)

== Installation ==

= From the WordPress Plugin Directory =

1. Go to **Plugins → Add New**.
2. Search for **Gulo Link-in-Bio**.
3. Click **Install Now**, then **Activate**.

= Manual installation =

1. Download the ZIP from the [plugin page](https://wordpress.org/plugins/simple-bio-links/) or [GitHub releases](https://github.com/habakuk007/simple-bio-links-Wordpress-Plugin/releases).
2. Go to **Plugins → Add New → Upload Plugin**.
3. Upload the ZIP and click **Install Now**, then **Activate**.

= First setup =

1. In the admin menu, click **Gulo Link-in-Bio**.
2. Set your **Name**, **Bio**, and **Profile Image**.
3. Choose a **Background** (gradient or solid color) and **Button Style**.
4. Under **Gulo Link-in-Bio Page**, select the WordPress Page that should show the profile.
5. Add your **Links** (title + URL). Drag rows to reorder.
6. Click **Save Settings**.
7. Visit the selected page — it will display the Gulo Link-in-Bio layout.

== Frequently Asked Questions ==

= Does this replace my theme? =

No. The plugin only affects the single WordPress Page you designate in settings. All other pages continue to use your active theme normally. The Gulo Link-in-Bio page is served as a completely standalone HTML document.

= Can I use it on more than one page? =

Currently the plugin supports one Gulo Link-in-Bio page at a time. Select the page under **Gulo Link-in-Bio → Gulo Link-in-Bio Page** in the settings.

= Which user roles can edit the settings? =

Administrators and Editors. The plugin grants both roles the `gulo_manage_settings` capability on activation. Authors, Contributors, and Subscribers cannot access the settings.

= Does it work with Yoast SEO? =

Yes. When Yoast SEO is active, the plugin hooks into Yoast's own filters (`wpseo_title`, `wpseo_opengraph_type`, `wpseo_opengraph_title`, `wpseo_robots`) instead of emitting competing HTML tags. There are no duplicate meta tags.

= Does it work with caching plugins? =

Yes. Whenever settings are saved, the plugin automatically purges the Gulo Link-in-Bio page from the following caches: WP Super Cache, WP Rocket, W3 Total Cache, WP Fastest Cache, LiteSpeed Cache, and Cache Enabler. It also calls `clean_post_cache()` for WordPress's built-in object cache. If you use a different caching plugin, purge the page manually after saving settings.

= Does it work with Matomo / other analytics? =

Yes. The plugin calls `wp_head()` and `wp_footer()` on the page, so any tracking code that hooks into those actions (Matomo via Connect Matomo, Google Site Kit, etc.) loads normally.

= Does the page appear in search results? =

By default, yes. To exclude it, check **Exclude this page from search engines (noindex)** in the SEO section of the settings. This adds a `noindex` meta tag (and instructs Yoast SEO to do the same if installed).

= How do I add a language not included in the plugin? =

1. Run `composer run make:pot` to re-extract strings (requires WP-CLI).
2. Copy `languages/simple-bio-links.pot` to `languages/simple-bio-links-{locale}.po`.
3. Add translations in a PO editor (e.g. [Poedit](https://poedit.net/)).
4. Run `composer run make:mo` to compile the MO binary.

Or use a plugin like [Loco Translate](https://wordpress.org/plugins/loco-translate/) to translate strings directly in the WordPress admin.

= Where can I report a bug or request a feature? =

On [GitHub Issues](https://github.com/habakuk007/simple-bio-links-Wordpress-Plugin/issues).

== Screenshots ==

1. The Gulo Link-in-Bio settings page — Profile and Page selection section.
2. The Appearance settings — background type, gradient/solid color pickers, and button style.
3. The Links manager — drag-to-reorder rows with active toggle and URL fields.
4. The live frontend profile page as seen by a visitor (desktop view).
5. The live frontend profile page on a mobile screen.

== Changelog ==

= 0.2.0 =
* Fixed plugin slug restored to `simple-bio-links` (main file renamed from `gulo-link-in-bio.php`) — required by WordPress.org plugin review
* Fixed text domain: all `gulo-link-in-bio` references replaced with `simple-bio-links` across PHP files, templates, and language files (POT + 4 PO/MO)
* Renamed PHP prefix from `GULO_` / `gulo_` to `GULOLI_` / `guloli_` to satisfy WordPress.org uniqueness requirement — affects class names (`GULOLI_Settings`, `GULOLI_Admin`, `GULOLI_Plugin`, `GULOLI_Frontend`), constants, option keys (`guloli_settings`, `guloli_links`), capability (`guloli_manage_settings`), template variables, asset handles, nonces, and settings group
* Fixed `background_type` and `button_style` sanitization: now uses strict allowlist via `sanitize_key()` + `in_array()` instead of `sanitize_text_field()`
* Updated all unit and integration tests to reflect renamed classes and constants
* Updated GitHub Sponsors username in `FUNDING.yml`

= 0.1.0 =
* Version bump

= 0.0.1 =
* Initial WordPress.org submission release
* Renamed plugin to "Gulo Link-in-Bio" — required by WordPress.org (name conflicts with existing plugins)
* Changed text domain from `link-in-bio` to `simple-bio-links` to match the plugin slug
* Renamed all language files accordingly (`simple-bio-links-*.po/mo/pot`)
* Changed version format from `1.0.0-alpha.x` to numeric-only (`0.0.1`) as required by WordPress.org

= 1.0.0-alpha.12 =
* Renamed plugin from "Link in Bio" to "Gulo Link-in-Bio" — required by WordPress.org (name conflict with existing plugin)
* All user-visible strings, admin menu, settings page, and documentation updated to "Gulo Link-in-Bio"
* Slug, text domain, option keys, and capability name unchanged — no data migration needed

= 1.0.0-alpha.11 =
* Fixed removed `load_plugin_textdomain()` call — WordPress auto-loads translations since 4.6
* Fixed "Tested up to" bumped to 6.9
* Fixed short description trimmed to ≤150 characters (readme.txt validator)
* Fixed excluded `phpunit.unit.xml` and `.wp-env.json` from release ZIP
* Build release ZIP now named `simple-bio-links-{version}.zip` (includes version in filename)
* Build replaced bash build script with PHP to avoid WSL conflicts on Windows

= 1.0.0-alpha.10 =
* Added `LICENSE` file (GPL-2.0-or-later)
* Added "Buy me a coffee" donation link on the settings page
* Added split PHPUnit test suite: unit tests (Brain\Monkey, no WP env) + integration tests
* Added 32 unit tests for `GULO_Settings` covering all sanitize methods
* Added `.wp-env.json` for Docker-based integration testing
* Fixed `sanitize_links()` to guard against `null` input (PHP 8.5 deprecation)
* Fixed WordPress.org plugin guidelines compliance: GPL headers, WP-bundled libs, dismissible notices, attribution, no trialware
* Fixed trademark: replaced "Linktree-style" with "link-in-bio page" throughout; README and readme.txt retain a clear "alternative to Linktree" reference
* Updated author URI, Plugin URI, and contributor username to correct values
* Updated all translation files with corrected plugin description and repository URLs
* Updated CI workflow with dedicated unit-test job (PHP 8.1–8.3, no database required)

= 1.0.0-alpha.9 =
* Added `uninstall.php` — removes plugin options and capability on plugin deletion
* Added `readme.txt` for WordPress.org Plugin Directory submission
* Added SVG plugin icon and `.wordpress-org/` artwork directory
* Added automated WordPress.org SVN deploy workflow (`deploy.yml`)
* Added `CONTRIBUTING.md` with full contribution and submission guidelines
* Fixed plugin description header: updated admin menu location reference
* Updated documentation: README, CLAUDE.md, Copilot instructions

= 1.0.0-alpha.8 =
* Added German (`de_DE`), French (`fr_FR`), Spanish (`es_ES`), and Ukrainian (`uk`) translations
* Added Yoast SEO integration via `wpseo_title`, `wpseo_opengraph_type`, `wpseo_opengraph_title`, `wpseo_robots` filters
* Added automatic page cache purging on settings save (6 caching plugins supported)
* Added Editor role access to settings via `gulo_manage_settings` custom capability
* Added "Edit Gulo Link-in-Bio" admin bar shortcut for logged-in Editors and Administrators
* Added `seo_noindex` option to exclude the page from search engines
* Added Legal section for Imprint and Privacy Policy footer links
* Moved admin menu from Settings submenu to top-level (position 81) so Editors can access it
* Version bump and release ZIP build via `composer run package`

= 1.0.0-alpha.7 =
* Converted from `[link_in_bio]` shortcode to a dedicated WordPress page template (`page-gulo-simple-bio-links.php`)
* Eliminated theme dependency — the page is now a fully standalone HTML document
* Added page selection dropdown in settings

= 1.0.0-alpha.6 =
* Initial pre-release
* Profile section (name, bio, avatar image via WordPress Media Library)
* Appearance section (gradient / solid background, button style, colors)
* Links manager (repeater with drag-to-reorder, active toggle, URL validation)
* Settings API integration with sanitize callbacks
* WPCS-clean codebase, PHP 7.4 compatible

== Upgrade Notice ==

= 1.0.0-alpha.12 =
The plugin has been renamed to "Gulo Link-in-Bio". No settings or data are affected — everything continues to work as before.

= 1.0.0-alpha.11 =
Maintenance release: WordPress.org compliance fixes and build tooling improvements. No functional changes.

= 1.0.0-alpha.10 =
Compliance and quality release. No functional changes for end users. Developers: unit tests can now be run locally without a WordPress environment via `composer run test:unit`.

= 1.0.0-alpha.9 =
Maintenance release: documentation and WordPress.org submission files only. No functional changes.

= 1.0.0-alpha.8 =
This release adds Editor access to the settings and moves the admin menu from Settings → Gulo Link-in-Bio to a top-level "Gulo Link-in-Bio" menu item. Bookmark updates may be needed. Cache is now purged automatically on save.

= 1.0.0-alpha.7 =
The shortcode `[link_in_bio]` has been replaced by a page template. After upgrading, open the previously used page, set its Template to "Gulo Link-in-Bio", and save. The shortcode will no longer render the profile.
