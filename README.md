# Gulo Link-in-Bio — WordPress Plugin

A link-in-bio page for WordPress — a self-hosted alternative to Linktree. Create a dedicated page,
select the **Gulo Link-in-Bio** page template, and your profile page is live — no shortcodes, no page builders.

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
   git clone https://github.com/habakuk007/Gulo-link-in-bio-Wordpress-Plugin.git
   link-in-bio
   ```

2. In WordPress admin, go to **Plugins → Installed Plugins** and activate **Gulo Link-in-Bio**.

### As a ZIP

1. [Download the latest release](https://github.com/habakuk007/Gulo-link-in-bio-Wordpress-Plugin/releases)
2. Go to **Plugins → Add New → Upload Plugin** and upload the ZIP.
3. Activate the plugin.

---

## Configuration

1. Go to **Gulo Link-in-Bio** in the WordPress admin menu (top-level item, below Settings).
2. Fill in your **Profile** details (name, bio, avatar image).
3. Set your **Appearance** (background gradient or solid color, button and text colors).
4. Add your **Links** — title, URL, and active toggle. Drag rows to reorder.
5. Click **Save Settings**.

---

## Usage

1. Go to **Pages → Add New** and give the page a title (e.g. "Links").
2. In the **Page Attributes** panel (classic editor) or the **Template** dropdown in the sidebar
   (block editor), select **Gulo Link-in-Bio**.
3. Publish the page.

The plugin serves a fully self-contained HTML page that bypasses your active theme entirely,
so your link-in-bio page looks the same regardless of which theme is installed.

---

## Access Control

The plugin registers a custom WordPress capability: `gulo_manage_settings`.

| Role | Access |
|------|--------|
| Administrator | Full access |
| Editor | Full access |
| Author, Contributor, Subscriber | No access |

The capability is granted automatically when the plugin is activated. It persists in the
WordPress roles table — removing it requires deactivating the plugin (which removes it cleanly).

When an Administrator or Editor views the Gulo Link-in-Bio page on the frontend, an
**Edit Gulo Link-in-Bio** shortcut appears in the WordPress admin bar.

---

## Programmatic usage

Settings and links are stored as WordPress options and can be set via `update_option()`:

```php
// Override settings programmatically
update_option( 'gulo_settings', array_merge(
    get_option( 'gulo_settings', array() ),
    array( 'profile_name' => 'My Brand' )
) );
```

### Available settings keys (`gulo_settings`)

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

### Links option (`gulo_links`)

Stored as a JSON string. Each item: `{"title": "...", "url": "...", "active": true}`.

---

## Hooks & Filters

### Actions

| Hook | When | Use |
|------|------|-----|
| `update_option_gulo_settings` | After settings save | Extend cache purging |

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

## Troubleshooting

**The Gulo Link-in-Bio page shows the theme instead of the plugin layout.**
Go to **Gulo Link-in-Bio** in wp-admin, scroll to the **Gulo Link-in-Bio Page** section, and select the
correct page. Alternatively confirm that the page's Template (Page Attributes) is set to
**Gulo Link-in-Bio** in the block editor.

**Translations are not loading.**
Check that MO files exist in `languages/` (`gulo-link-in-bio-{locale}.mo`). If you added or changed
PO files, run `composer run make:mo` to recompile them. Ensure the WordPress site language
matches the locale (e.g., `de_DE`).

**The settings page is not visible for an Editor.**
The `gulo_manage_settings` capability must be present in the `editor` role. Deactivate and
reactivate the plugin to re-grant it. Verify with:
`get_role('editor')->has_cap('gulo_manage_settings')` in a PHP snippet.

**Changes to settings are not reflected on the frontend.**
A caching plugin may be serving a stale page. Saving settings normally triggers automatic cache
purging for WP Super Cache, WP Rocket, W3 Total Cache, WP Fastest Cache, LiteSpeed Cache, and
Cache Enabler. If your caching plugin is not listed, purge it manually or hook into
`update_option_gulo_settings`.

**Yoast SEO shows wrong title or OG tags for the Gulo Link-in-Bio page.**
Ensure you are running version 1.0.0-alpha.7 or later. The plugin hooks into Yoast's own filters
(`wpseo_title`, `wpseo_opengraph_type`, `wpseo_opengraph_title`, `wpseo_robots`) to avoid
duplicate meta tags.

---

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for development setup, coding standards, and the PR process.

---

## License

[GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html)
