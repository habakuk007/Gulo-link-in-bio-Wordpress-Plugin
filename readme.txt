=== Gulo Link-in-Bio ===
Contributors: habakuk
Tags: link in bio, linktree, links, profile, social links
Requires at least: 6.0
Tested up to: 7.0
Stable tag: 0.3.1
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://trumpkin.de/donate

A link-in-bio page for WordPress — a self-hosted alternative to Linktree and similar services. Assign the template to any Page. No shortcodes, no page builders.

== Description ==

**Gulo Link-in-Bio** turns a WordPress Page into a dedicated social landing page, without touching your active theme. It is a self-hosted alternative to Linktree and is ideal for social media bios, creator profiles, and landing pages that collect all your important links in one place.

This plugin is 100% free and GPL licensed, with no Pro version, no ads, and no aggressive upsells.

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
* **Legal footer** — optional Imprint and Privacy Policy links for GDPR / Impressum compliance
* **Works cleanly with Yoast SEO** — avoids duplicate meta tags
* **Cache friendly** — automatically clears common cache plugins after saving settings
* **Editor access** — Administrators and Editors can manage the settings via a custom capability
* **Admin bar shortcut** — logged-in users with access see an "Edit Gulo Link-in-Bio" link directly on the frontend page
* **Fully accessible** — WCAG 2.2 AA friendly with skip link, semantic landmarks, visible focus, and reduced-motion support
* **Translated** — ships with German, French, Spanish, and Ukrainian translations
* **No tracking, no ads, no Pro version** — fully free and open source (GPL-2.0-or-later)

== Installation ==

= From the WordPress Plugin Directory =

1. Go to **Plugins → Add New**.
2. Search for **Gulo Link-in-Bio**.
3. Click **Install Now**, then **Activate**.

= Manual installation =

1. Download the ZIP from the [plugin page](https://wordpress.org/plugins/gulo-link-in-bio/) or [GitHub releases](https://github.com/habakuk007/Gulo-link-in-bio-Wordpress-Plugin/releases).
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

Visit [translate.wordpress.org/projects/wp-plugins/gulo-link-in-bio/](https://translate.wordpress.org/projects/wp-plugins/gulo-link-in-bio/), select your locale, and contribute translations. Once at least 90% of the strings are approved by a translation editor, a language pack is generated automatically and delivered to WordPress sites within 30 minutes — no action required from the plugin author.

If you want to preview translations locally before they are approved, you can use a plugin like [Loco Translate](https://wordpress.org/plugins/loco-translate/).

= Where can I report a bug or request a feature? =

On [GitHub Issues](https://github.com/habakuk007/Gulo-link-in-bio-Wordpress-Plugin/issues).

== Screenshots ==

1. The Gulo Link-in-Bio settings page — Profile and Page selection section.
2. The Appearance settings — background type, gradient/solid color pickers, and button style.
3. The Links manager — drag-to-reorder rows with active toggle and URL fields.
4. The legal and SEO settings area.
5. The live frontend profile page as seen by a visitor (desktop view, with solid buttons, imprint and privacy policy).
6. The live frontend profile page on a mobile screen (glass buttons).
7. The live frontend profile page on a mobile screen (solid buttons).

== Changelog ==

= 0.3.1 =
* Added WordPress.org packaging assets and compliance documentation for submission readiness.
* Committed `package-lock.json` and `composer.lock`, and pinned PHPUnit for reproducible installs and stable CI.
* Updated CI workflows with Subversion support before WP test bootstrap and refined deployment configuration.
* Cleaned up plugin metadata for WordPress.org review and compatibility validation.

See `changelog.txt` for older entries.

== Upgrade Notice ==

= 0.3.0 =
Pre-final version; not yet stable. Please uninstall completely and reinstall.
