# Gulo Link-in-Bio — Claude Instructions

## What This Project Is

A **WordPress plugin** that provides a link-in-bio page — a self-hosted alternative to Linktree.  
Users go to **Gulo Link-in-Bio** (top-level admin menu, position 81) to configure their profile, colors, and links.  
Both Administrators and Editors can access the settings page via the custom `guloli_manage_settings` capability.  
The result is displayed by creating a WordPress Page and selecting **Gulo Link-in-Bio** from the Template dropdown.

## File Map

```
gulo-link-in-bio.php                   Plugin header + bootstrap (constants, requires, hooks)
uninstall.php                     Runs on plugin deletion: removes options + capability
readme.txt                        WordPress.org directory listing (required for submission)
includes/class-guloli-plugin.php     Singleton boot class; activate/deactivate statics
includes/class-guloli-settings.php   Settings helper: get(), get_links(), sanitize callbacks
includes/class-guloli-admin.php      Admin menu, Settings API registration, page render
includes/class-guloli-frontend.php   Page template registration + lazy asset enqueueing
templates/page-simple-bio-links.php         Full HTML page (DOCTYPE → wp_footer); sets $guloli_settings/$guloli_links
templates/display.php             Accessible link-in-bio HTML partial (profile + nav + footer)
assets/css/frontend.css           Link-in-bio CSS (custom properties for theming)
assets/css/admin.css              Admin settings page styles
assets/js/admin.js                Links repeater, WP media uploader, color pickers
tests/bootstrap.php               PHPUnit bootstrap for integration tests (needs WP env)
tests/unit/bootstrap.php          PHPUnit bootstrap for unit tests (Brain\Monkey, no WP needed)
tests/unit/Test_GULOLI_Settings.php  Unit tests for GULOLI_Settings (32 tests, Brain\Monkey)
tests/integration/class-test-guloli-plugin.php  Integration tests (WP_UnitTestCase)
phpcs.xml                         PHPCS / WPCS configuration
phpunit.xml.dist                  PHPUnit config — integration tests (tests/integration/)
phpunit.unit.xml                  PHPUnit config — unit tests (tests/unit/)
.wp-env.json                      wp-env config for Docker-based integration tests
composer.json                     PHP dev dependencies (WPCS, PHPUnit, PHPCompatibility)
package.json                      JS dev dependencies (ESLint, Stylelint)
.editorconfig                     Editor settings (tabs, UTF-8, LF)
.gitignore                        Ignored paths
.github/copilot-instructions.md   GitHub Copilot project overview
.github/instructions/             TRACKED COPY of .agents/instructions/ - never edit directly
.github/agents/                   TRACKED COPY of .agents/agents/ - never edit directly
.github/workflows/ci.yml          GitHub Actions: lint + test matrix + ai-config-sync check
AGENTS.md                         Project guide for all AI agents (tables + MCP servers)
opencode.json                     OpenCode config (reads AGENTS.md)
.mcp.json / .vscode/mcp.json      MCP server definitions (Claude Code / VS Code)
.vscode/settings.json             VS Code editor settings
.vscode/extensions.json           VS Code recommended extensions
.agents/                          SINGLE SOURCE OF TRUTH for AI config (see ai-tool-sync.instructions.md)
.agents/instructions/             Instruction files (WP, WPCS, CSS, a11y, readme.txt, translations, AI sync)
.agents/agents/                   Agent definitions (code-review, wp-security-review, refine-issue)
.agents/skills/                   Skills (gh, gh-bot, php-lint, translate-de, wp-release, grill-me, cross-review)
.claude/agents/ + .opencode/agents/  Read-only cross-reviewer agents used headless by cross-review
.agents/scripts/sync-github-copies.sh  Refreshes/checks the .github copies
.claude/commands/                 Slash commands, one per agent and skill
```

## Two WordPress Options

| Option key        | Type          | Description                              |
|-------------------|---------------|------------------------------------------|
| `guloli_settings` | `array`       | Profile, colors, background              |
| `guloli_links`    | `string` JSON | Array of `{title, url, active}` objects  |

Settings keys in `guloli_settings`:
`profile_name`, `profile_bio`, `profile_image`, `background_type`, `background_color`,
`gradient_start`, `gradient_end`, `button_style`, `button_bg_color`, `button_text_color`,
`profile_text_color`, `page_id`, `seo_noindex`

## Translation Conventions

The plugin ships translations in `languages/`. German has two locale variants:

| Locale file | WordPress locale | Register form | Voice |
|---|---|---|---|
| `gulo-link-in-bio-de.po` | `de` | informal — **du / dein** | "Konfiguriere dein Profil …" |
| `gulo-link-in-bio-de_DE_formal.po` | `de_DE_formal` | formal — **Sie / Ihr** | "Konfigurieren Sie Ihr Profil …" |

When writing or reviewing German translations, use **du/dein** for the `de` file and **Sie/Ihr** for the `de_DE_formal` file. After editing any `.po` file, run `composer run make:mo` to recompile the `.mo` binaries.

## Coding Rules

- **Prefix**: classes `GULOLI_`, functions/hooks/globals/options `guloli_`; JS is wrapped in an IIFE — no globals exposed
- **Text domain**: `gulo-link-in-bio` in every i18n call
- **Capability**: `guloli_manage_settings` for all permission checks in this plugin — never `manage_options`
- **Escape outputs**: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- **Sanitize inputs**: `sanitize_text_field()`, `sanitize_hex_color()`, `esc_url_raw()`
- **No inline JS/CSS** — CSS custom properties are injected via `wp_add_inline_style()` in `GULOLI_Frontend`
- **Nonce + capability** for any write action (Settings API handles this via `settings_fields()`)
- **Assets** — registered and enqueued lazily in `maybe_enqueue_assets()` (only on the template page)
- **Cache** — `GULOLI_Admin::purge_page_cache()` is called automatically on `update_option_GULOLI_Settings`; it clears WP object cache + 6 known caching plugins
- **PHP 7.4+** — avoid features that require PHP 8.x unless explicitly asked

## Accessibility Requirements

- Skip link (`#lib-links`) must remain the first focusable element in the template output
- `<h1>` for profile name, `<nav>` with `aria-label` for links, `<section>` for profile
- All link buttons must have `:focus-visible` styles with minimum 3:1 contrast
- External links (`target="_blank"`) must announce "(opens in new tab)" via `aria-label`
- `prefers-reduced-motion` must suppress `transform` animations
- Image `alt` must read "Profile photo of [Name]"

## Development Commands

```bash
# PHP dev dependencies
composer install

# Lint PHP (WPCS)
composer run lint:php

# Fix PHP style
composer run fix:php

# Run unit tests (Brain\Monkey — no WordPress environment needed)
composer run test:unit

# Run integration tests (needs WP_TESTS_DIR set up via bin/install-wp-tests.sh)
WP_TESTS_DIR=/tmp/wordpress-tests-lib composer run test

# JS/CSS dev dependencies
npm install

# Lint JS
npm run lint:js

# Lint CSS
npm run lint:css

# Re-extract translatable strings to POT (requires WP-CLI)
composer run make:pot

# Compile all PO files to MO binaries (requires WP-CLI)
composer run make:mo
```

## Agents

Definitions in `.agents/agents/`; each has a slash command in `.claude/commands/`.

| Agent | Command | Purpose |
|---|---|---|
| [code-review](.agents/agents/code-review.agent.md) | `/code-review` | WPCS compliance, security, backwards-compat review |
| [wp-security-review](.agents/agents/wp-security-review.agent.md) | `/wp-security-review` | XSS, CSRF, SQLi, privilege escalation audit |
| [refine-issue](.agents/agents/refine-issue.agent.md) | `/refine-issue` | Add acceptance criteria and technical notes to a GitHub issue |

## Skills

Definitions in `.agents/skills/`; each has a slash command in `.claude/commands/`.

| Command | Skill | Purpose |
|---|---|---|
| `/gh` | [gh/SKILL.md](.agents/skills/gh/SKILL.md) | GitHub CLI: issues, PRs, releases, labels |
| `/gh-bot` | [gh-bot/SKILL.md](.agents/skills/gh-bot/SKILL.md) | Open PRs and PR comments as bot identity `habakuk-ai`; commits and pushes stay `habakuk007`, so `habakuk007` can approve and merge |
| `/php-lint` | [php-lint/SKILL.md](.agents/skills/php-lint/SKILL.md) | PHPCS/PHPCBF for WordPress Coding Standard |
| `/translate-de` | [translate-de/SKILL.md](.agents/skills/translate-de/SKILL.md) | German translation (du/Sie) per Polyglots Style Guide and Glossary |
| `/wp-release` | [wp-release/SKILL.md](.agents/skills/wp-release/SKILL.md) | Release workflow (version bump, release PR, tag, deploy) |
| `/grill-me` | [grill-me/SKILL.md](.agents/skills/grill-me/SKILL.md) | Stress-test a plan through relentless questions |
| `/cross-review` | [cross-review/SKILL.md](.agents/skills/cross-review/SKILL.md) | Opt-in second-AI review of a pull request: post as bot, evaluate, reply, fix valid findings (one round) |

PRs are opened with `bash .agents/skills/gh-bot/gh-bot.sh pr <branch> "<title>" "<body>"` after a plain `git push`. Never approve or merge as the bot.

## AI Config Sync

`.agents/` is the single source of truth. Edit instructions and agents **only** there, then run `bash .agents/scripts/sync-github-copies.sh` to refresh `.github/instructions/` and `.github/agents/` (Copilot reads them there). CI job `ai-config-sync` fails if the copies differ. When adding or removing an instruction, agent, skill or MCP server, update the tables in `CLAUDE.md`, `AGENTS.md` and `.github/copilot-instructions.md` - see `.agents/instructions/ai-tool-sync.instructions.md`.

## What NOT to Change Without Asking

- The option names `guloli_settings` / `guloli_links` — changing them loses saved data
- `templates/page-simple-bio-links.php` — the path is hardcoded in `GULOLI_Frontend::load_template()`; renaming the file breaks the template lookup
- The prefixes `GULOLI_` (classes) / `guloli_` (functions, hooks, globals, options) — collision-avoidance contract
- The `sanitize_*` callbacks in `GULOLI_Settings` — they protect data integrity
- The capability slug `guloli_manage_settings` — it is stored in the WordPress roles table; renaming it silently locks out Editors

## Instruction Files Referenced

Domain instructions (from [awesome-copilot](https://awesome-copilot.github.com/instructions/) and project-specific ones)
live in `.agents/instructions/` (`.github/instructions/` is a tracked copy for Copilot). When working here, follow them:

- **WordPress**: `.agents/instructions/wordpress.instructions.md`
- **WPCS/PHPCS**: `.agents/instructions/wpcs.instructions.md`
- **WordPress.org**: `.agents/instructions/wordpress-org.instructions.md`
- **Color/CSS**: `.agents/instructions/html-css-style-color-guide.instructions.md`
- **Accessibility**: `.agents/instructions/a11y.instructions.md`
- **readme.txt / changelog.txt**: `.agents/instructions/readme-txt.instructions.md`
- **Translations**: `.agents/instructions/translation-general.instructions.md`, `translation-de.instructions.md`
- **AI config files**: `.agents/instructions/ai-tool-sync.instructions.md`, `agents.instructions.md`, `agent-skills.instructions.md`
