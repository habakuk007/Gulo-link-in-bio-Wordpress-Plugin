# GitHub Copilot — Project Instructions

## Project Overview

**Gulo Link-in-Bio** is a WordPress plugin that provides a link-in-bio page — a self-hosted alternative to Linktree.
Users configure a profile (name, bio, avatar), appearance (colors, gradients), and a list of
links via the **Gulo Link-in-Bio** menu in wp-admin (top-level menu, accessible to Administrators
and Editors). The profile is displayed by creating a WordPress Page and assigning the
**Gulo Link-in-Bio** page template — no shortcodes, no page builders required.

See `AGENTS.md` for the full project guide and `CLAUDE.md` for the Claude Code specifics.

## Coding Standards Applied Automatically

Domain instructions live in `.github/instructions/`. This folder is a **tracked copy** of
`.agents/instructions/` (source of truth) — never edit it directly; refresh with
`bash .agents/scripts/sync-github-copies.sh` (CI job `ai-config-sync` checks it).

| File | Applies to | Purpose |
|------|-----------|---------|
| `wordpress.instructions.md` | PHP, JS, CSS, JSON | WordPress coding standards, security, i18n |
| `wpcs.instructions.md` | `**/*.php` | WPCS/PHPCS reference: run checks, ruleset details, inline suppression |
| `html-css-style-color-guide.instructions.md` | `**/*.css`, `**/*.html`, `**/*.js` | Color palette rules, 60-30-10 rule, gradients |
| `a11y.instructions.md` | `**` | WCAG 2.2 AA accessibility, ARIA, skip links |
| `readme-txt.instructions.md` | `readme.txt`, `changelog.txt` | WordPress.org readme format, changelog from git log, version sync |
| `wordpress-org.instructions.md` | `**` | WordPress.org plugin directory and submission compliance |
| `translation-general.instructions.md` | `languages/**` | Universal translation rules (placeholders, plurals, GlotPress) |
| `translation-de.instructions.md` | `languages/*de*.po` | German style guide: du (`de`) vs Sie (`de_DE_formal`) |
| `ai-tool-sync.instructions.md` | `CLAUDE.md`, `AGENTS.md`, `.agents/**`, `.mcp.json` | Keeping AI-tool config files in sync |
| `agents.instructions.md` | `**/*.agent.md` | Writing custom agent files |
| `agent-skills.instructions.md` | `**/.agents/skills/**/SKILL.md` | Writing skills |

## Agents and Skills

Agents (`.github/agents/`, copy of `.agents/agents/`): `code-review`, `wp-security-review`, `refine-issue`.
Skills (`.agents/skills/`): `gh`, `gh-bot`, `php-lint`, `translate-de`, `wp-release`, `grill-me`.

## Plugin Architecture

```
gulo-link-in-bio.php              ← Plugin entry point (header + bootstrap)
includes/
  class-guloli-plugin.php         ← Singleton boot class, activation/deactivation, capability grants
  class-guloli-settings.php       ← Static helper: get(), get_links(), sanitize callbacks
  class-guloli-admin.php          ← Top-level admin menu, Settings API, settings page HTML, cache purge
  class-guloli-frontend.php       ← Page template registration, lazy asset enqueue, SEO meta, admin bar
templates/
  page-simple-bio-links.php       ← Full standalone HTML page (DOCTYPE → wp_footer)
  display.php                     ← Accessible profile + links + footer partial
assets/
  css/frontend.css                ← Link-in-bio page frontend design (CSS custom props)
  css/admin.css                   ← Admin settings page styles
  js/admin.js                     ← Links repeater, media uploader, color pickers
languages/
  gulo-link-in-bio.pot            ← Translation template (regenerate with: composer run make:pot)
  gulo-link-in-bio-{de,de_DE_formal,es_ES,fr_FR,uk}.po/.mo
tests/
  unit/                           ← Brain\Monkey unit tests (no WordPress needed)
  integration/                    ← WP_UnitTestCase integration tests
```

## Key Conventions

- **Prefix**: classes `GULOLI_`; functions, hooks, globals and options `guloli_`.
- **Text domain**: `gulo-link-in-bio` — always use this in i18n functions.
- **Options**: `guloli_settings` (array) and `guloli_links` (JSON string).
- **Capability**: `guloli_manage_settings` — custom cap granted to Administrators and Editors on activation. Use this (not `manage_options`) for all capability checks in this plugin.
- **Escape on output**: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`.
- **Sanitize on input**: `sanitize_text_field()`, `sanitize_hex_color()`, `esc_url_raw()`.
- **No inline scripts/styles** except the CSS custom property block injected via `wp_add_inline_style()` in `GULOLI_Frontend`.
- **Assets**: register with `wp_register_*`, enqueue lazily in `maybe_enqueue_assets()` (only when the template is active).
- **Cache**: saving settings triggers `GULOLI_Admin::purge_page_cache()`, which clears the WP object cache and known caching plugins.
- **PHP 7.4+**: avoid features that require PHP 8.x.
- **Accessibility**: skip link, semantic landmarks, WCAG 2.2 AA contrast, `prefers-reduced-motion`.

## Do Not Change

- Option names `guloli_settings` / `guloli_links`
- `templates/page-simple-bio-links.php` (path hardcoded in `GULOLI_Frontend::load_template()`)
- Prefixes `GULOLI_` / `guloli_`
- The `sanitize_*` callbacks in `GULOLI_Settings`
- The capability slug `guloli_manage_settings`

## Development Commands

```bash
composer install            # PHP dev dependencies
composer run lint:php       # WPCS
composer run fix:php        # auto-fix PHP style
composer run test:unit      # unit tests (no WordPress needed)
composer run test           # integration tests (needs $WP_TESTS_DIR)
npm install && npm run lint:js && npm run lint:css
composer run make:pot       # re-extract strings → languages/gulo-link-in-bio.pot
composer run make:mo        # compile all PO files → MO binaries
bash .agents/scripts/sync-github-copies.sh --check   # AI config copies in sync?
```

## When Adding Features

1. Put business logic in a class under `includes/`.
2. Register hooks in the constructor, not at file level.
3. Add sanitize callbacks for any new option.
4. Wrap user-visible strings in `esc_html__( 'Text', 'gulo-link-in-bio' )`.
5. Write or update tests in `tests/`.
6. Check WCAG 2.2 AA for any UI additions.
7. Use `guloli_manage_settings` (not `manage_options`) for any new capability check.
8. After adding new strings, run `composer run make:pot` and update the PO files.
