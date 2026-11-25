---
applyTo: '**'
description: 'WordPress.org plugin directory and submission compliance rules for plugins hosted on wordpress.org.'
---

# WordPress.org Plugin Directory — Copilot Instructions

**Goal:** Keep plugin submissions compatible with the official WordPress.org directory and reviewer expectations. This file captures directory-specific requirements, release discipline, licensing, and marketplace conduct.

## Official resources

- Plugin Directory home: https://developer.wordpress.org/plugins/wordpress-org/
- Detailed Plugin Guidelines: https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/
- Plugin Developer FAQ: https://developer.wordpress.org/plugins/wordpress-org/plugin-developer-faq/
- Common Issues: https://developer.wordpress.org/plugins/wordpress-org/common-issues/
- Planning, Submitting, and Maintaining Plugins: https://developer.wordpress.org/plugins/wordpress-org/planning-submitting-and-maintaining-plugins/
- Using Subversion: https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/
- Using the MCP Server: https://developer.wordpress.org/plugins/wordpress-org/using-the-mcp-server/
- Plugin Assets: https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/

## Review & SVN reminders
- Reviewers and developers are required to follow the plugin guidelines, but a successful review does not guarantee the plugin is free of all security issues or guideline violations.
- Continue checking your code with tools such as PHPCS/WPCS (https://github.com/WordPress/WordPress-Coding-Standards) and Plugin Check (https://wordpress.org/plugins/plugin-check/).
- If an issue arises later, WordPress.org may close the plugin and notify you to fix it; this is standard operating procedure for all plugins.
- SVN is a release system, not a development system. Push only ready-to-use versions of your plugin.
- SVN user IDs are the same as your WordPress.org login ID, not your email address, and they are case sensitive.
- Set up your SVN credentials in the Account & Security section of your WordPress profile: https://profiles.wordpress.org/me/profile/edit/group/3/?screen=svn-password
- Your `readme.txt` content determines what is shown on your public WordPress.org plugin page.
- Plugin banners, screenshots, and icons are managed through the plugin assets folder.

## Key WordPress.org directory rules

### Licensing and third-party content
- All files distributed in the plugin must be GPLv2-or-later or a GPL-compatible license.
- This includes PHP code, JavaScript, CSS, images, fonts, and bundled libraries.
- Verify the license of every third-party library or asset before including it.

### Developer responsibility
- The plugin author is responsible for all contents of the plugin directory package.
- Ensure bundled files, documentation, and external service usage comply with WordPress.org guidelines and terms of service.
- If in doubt, ask `plugins@wordpress.org` before submission.

### Release and SVN discipline
- SVN is a release repository, not a development repository.
- Only commit deployable code, stable releases, betas, or release candidates.
- Avoid frequent trivial commits and update messages such as "fix" or "cleanup".
- Increment the plugin version for each new release so users are alerted to updates.
- The plugin must be complete and functional at submission time.

### Human readable code and source access
- Do not obfuscate or mangle plugin code; human-readable source is required.
- Include source files and build tool references, or provide a public link to the development repository.
- If a build step exists, document how the plugin is built and what files are shipped.

### No trialware or locked functionality
- Do not ship plugin code that disables or hides functionality pending payment or a trial period.
- Paid external services are permitted, but the plugin code itself must remain fully available.
- Premium add-ons should be separate plugins hosted outside the directory if needed.

### Tracking, telemetry, and external communication
- Plugins may not track users without explicit, opt-in consent.
- Do not contact external servers for telemetry, usage tracking, or hidden analytics unless the user has agreed.
- Document any data collection or service communication clearly in the readme and privacy policy.

### Executable third-party code
- Plugins may not load executable code from third-party systems unless the plugin clearly acts as a service interface.
- Do not install or update plugins, themes, or code from external servers other than WordPress.org.
- Avoid iframe-based admin pages; use WordPress APIs for remote or service-based interactions.

### Optional credits and links
- Any front-end credits, "Powered by" links, or branding must be entirely optional and default to off.
- The plugin must not require attribution or forced links to function.

### Admin notices and dashboard behavior
- Do not hijack the admin dashboard with persistent or site-wide nags.
- Notices, upgrade prompts, and alerts should be minimal, contextual, and dismissible.
- Avoid advertising-like content in admin pages; respect the WordPress admin experience.

### Readmes and public pages
- Readme pages must not spam or keyword-stuff.
- Limit tags and avoid competitor brand names as tags; no more than 12 tags total, with only the first 5 displayed.
- Affiliate links must be disclosed and direct; do not cloak or redirect affiliate URLs.
- Keep readmes written for people, not search engines.

### Bundled libraries
- Use WordPress bundled libraries where possible; do not ship duplicate copies of default WP libraries.
- Reference WordPress’ registered scripts/styles rather than bundling your own versions of the same libraries when feasible.

### Trademarks and naming
- Respect trademarks, copyrights, and project names in plugin slugs, branding, and marketing.
- Avoid plugin slugs or names that imply affiliation with a trademarked product unless you are authorized.
- Choose original branding that is clear and memorable.

## Practical compliance checklist
- [ ] Plugin code and assets are GPL-compatible.
- [ ] Plugin package is complete and deployable at submission.
- [ ] SVN commits are release-quality only.
- [ ] Version numbers increment with each release.
- [ ] No obfuscated or mangled code is shipped.
- [ ] No trial/gateware functionality is present.
- [ ] External tracking is opt-in only.
- [ ] No third-party executable code is loaded outside WordPress.org rules.
- [ ] Optional front-end credits default to hidden/off.
- [ ] Admin notices are contextual and dismissible.
- [ ] Readme content is non-spammy and tag usage is appropriate.
- [ ] Default WP libraries are used instead of duplicated bundles.
- [ ] Plugin naming respects trademarks and project ownership.

## Common review rejections (checklist before every release)

Source: [Common Issues](https://developer.wordpress.org/plugins/wordpress-org/common-issues/). The reviewers' most frequent grounds are unescaped output, unsanitized input and form handling without a nonce.

- **Input/output:** sanitize and validate input, escape late on output. Never process whole `$_POST`/`$_GET`/`$_REQUEST`; read single keys. `esc_url_raw()` sanitizes, `esc_url()` escapes. `__()`/`_e()` do **not** escape — use `esc_html__()`, `esc_attr__()`, `esc_html_e()`. Use `wp_kses_post()`/`wp_kses()` for HTML, `wp_json_encode()` for JSON, `$wpdb->prepare()` for SQL.
- **Direct access:** `if ( ! defined( 'ABSPATH' ) ) { exit; }` in every PHP file that executes code (`includes/*.php`, `templates/*.php`; `uninstall.php` checks `WP_UNINSTALL_PLUGIN`).
- **Prefixes:** prefix functions, classes, constants, options, globals. **Two- or three-letter prefixes are not accepted**, and `wp_`, `__` and a bare `_` are forbidden. This plugin uses `GULOLI_` / `guloli_` (do not shorten it).
- **i18n:** text, context and domain must be literal strings; the text domain must equal the slug (`gulo-link-in-bio`).
- **Assets:** enqueue with `wp_enqueue_script()`/`wp_enqueue_style()` (and the inline variants); no remote JS/CSS/images (exceptions: GPL-compatible font CDNs, service APIs, oEmbed). Do not ship libraries WordPress already bundles (jQuery, …).
- **Plugin standards:** main file named after the slug (`gulo-link-in-bio.php`) with complete headers; `readme.txt` complete and valid; GPL-compatible license in header **and** readme, identical; `Stable tag` equals `Version` (and `GULOLI_VERSION`); no update checkers or phone-home code; no dev folders (tests, node tooling, `.agents/`, AI config files) in the release — controlled by `export-ignore` in `.gitattributes`; source or a public link for minified files.
- **External services:** the plugin makes no remote requests. If one is ever added, document it in `readme.txt` (what it does, link, terms and privacy links).
- **Test with `WP_DEBUG` on** and run `wp plugin check gulo-link-in-bio`. WP-CLI runs only the static checks by default; add `--require=./wp-content/plugins/plugin-check/cli.php` to include the runtime checks. To silence a known false positive use `--ignore-codes=<code>` or a `.pcpignore` file (Plugin Check 2.2.0+), or report it at github.com/WordPress/plugin-check. Passing the "Plugin repo" category is what the review expects; the other categories are best practice. Plugin Check uses its own ruleset, so a `phpcs.xml` exclusion does not silence it — use an inline `// phpcs:disable <Sniff> -- <reason>`.
- **Developer account:** enable two-factor authentication on your wordpress.org account and use a unique password for SVN (see the [Security Handbook](https://developer.wordpress.org/apis/security/)).

## Automated security review and compliance claims

- **Automated security review (since June 2026, [page](https://developer.wordpress.org/plugins/wordpress-org/automated-security-review/)):** every release has a cooldown in which AI models and Jetpack Scan analyse the diff. If the highest risk score reaches the blocking threshold, that version is **withheld from the update API** and all committers get an email with file and line. Only that version is affected; older versions keep serving. Fix the issue and publish a **new** version (fixing is usually faster than appealing; false positives: reply to the email). Consequence for releases: run `/wp-security-review` before tagging, and do not assume a pushed tag has reached users.
- **Compliance disclaimers ([page](https://developer.wordpress.org/plugins/wordpress-org/compliance-disclaimers/), guideline 9):** never claim that the plugin creates, provides or guarantees legal compliance (GDPR, ADA, cookie law). Say it "helps" or "assists". This applies to `readme.txt`, descriptions, assets and the plugin name.

## Plugin assets (banner, icon, screenshots)

Source: [Plugin Assets](https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/).

- They live in the **root-level `assets/` folder of the SVN repo** (next to `trunk/` and `tags/`), never in `trunk/assets`. In this repo they are kept in `.wordpress-org/` and published there by the deploy workflow (`ASSETS_DIR`). The plugin's own `assets/` folder is the plugin's CSS/JS and is something else.
- Banner `banner-772x250.(png|jpg)` (+ `banner-1544x500` for retina, only in addition to the small one); icon `icon-128x128` / `icon-256x256` or `icon.svg` (with a PNG fallback); screenshots `screenshot-1.png` … in lower case, matching the `== Screenshots ==` list. Limits: banner 4 MB, icon 1 MB, screenshot 10 MB. No official logos you do not own.
- If images download instead of displaying: `svn propset svn:mime-type image/png *.png` (only affects new files unless re-run).
- Changes appear after minutes, up to about 6 hours.
