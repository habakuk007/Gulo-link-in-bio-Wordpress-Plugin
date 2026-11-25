# AGENTS.md — Gulo Link-in-Bio

Project guide for all AI agents (OpenCode, Claude Code, GitHub Copilot, VS Code).

## Commands

```bash
# PHP dev dependencies
composer install

# Lint PHP (WPCS + PHP 7.4+ compatibility, phpcs.xml)
composer run lint:php

# Auto-fix PHP style
composer run fix:php

# Unit tests (Brain\Monkey, no WordPress environment needed)
composer run test:unit

# Integration tests (needs WP_TESTS_DIR, see bin/install-wp-tests.sh)
WP_TESTS_DIR=/tmp/wordpress-tests-lib composer run test

# JS/CSS dev dependencies and linting
npm install
npm run lint:js
npm run lint:css

# Translations (WP-CLI comes from Composer)
composer run make:pot   # -> languages/gulo-link-in-bio.pot
composer run make:mo    # compile all languages/*.po to .mo

# Check that the .github copies match .agents/
bash .agents/scripts/sync-github-copies.sh --check

# Tests of the cross-review helper script (Python 3.9+, standard library only)
python -m unittest discover -s .agents/skills/cross-review/scripts
```

No build system. Composer and npm are used for dev tooling only.

## Project Structure

```
gulo-link-in-bio.php              <- Plugin header + bootstrap (constants, requires, hooks)
uninstall.php                     <- Removes options + capability on plugin deletion
readme.txt                        <- WordPress.org directory listing
includes/
  class-guloli-plugin.php         <- Singleton boot class; activate/deactivate statics
  class-guloli-settings.php       <- get(), get_links(), sanitize callbacks
  class-guloli-admin.php          <- Admin menu, Settings API, page render, cache purge
  class-guloli-frontend.php       <- Page template registration, lazy asset enqueueing
templates/
  page-simple-bio-links.php       <- Full HTML page (DOCTYPE -> wp_footer)
  display.php                     <- Accessible profile + links + footer partial
assets/{css,js}/                  <- frontend.css, admin.css, admin.js
languages/                        <- .pot + de, de_DE_formal, es_ES, fr_FR, uk (.po/.mo)
tests/{unit,integration}/         <- PHPUnit (Brain\Monkey / WP_UnitTestCase)
.agents/                          <- instructions, agents, skills (source of truth for AI config)
```

## Architecture

Administrators and Editors (capability `guloli_manage_settings`) configure profile, colors and links under the top-level admin menu **Gulo Link-in-Bio**. Data lives in two options: `guloli_settings` (array) and `guloli_links` (JSON string of `{title, url, active}`). The result is shown by assigning the **Gulo Link-in-Bio** template to a WordPress Page. Assets are enqueued lazily only on that page; CSS custom properties are injected with `wp_add_inline_style()`. Saving settings purges the page cache (`GULOLI_Admin::purge_page_cache()`).

## Branches and Releases

- `main` = released state. Version tags are created on `main` and trigger the deploy workflow.
- `develop` = integration branch; work in short-lived `feature/…`, `fix/…`, `chore/…` branches and open PRs **against `develop`**.
- Release: `release/X.Y.Z` -> `develop` -> `main`, then tag (see `/wp-release`).
- AI-authored PRs and PR comments go through `/gh-bot` (identity `habakuk-ai`); commits and pushes stay `habakuk007`. Never approve or merge as the bot.

## Key Conventions

| Item | Value |
|------|-------|
| Class prefix | `GULOLI_` |
| Function / hook / global / option prefix | `guloli_` |
| Text domain | `gulo-link-in-bio` |
| Capability | `guloli_manage_settings` (never `manage_options`) |
| Options | `guloli_settings`, `guloli_links` |
| PHP | 7.4+ (no 8.x-only features) |
| Escaping / sanitizing | `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()` / `sanitize_text_field()`, `sanitize_hex_color()`, `esc_url_raw()` |
| JS | IIFE, no globals |
| German | `de` = du/dein, `de_DE_formal` = Sie/Ihr |

Accessibility (WCAG 2.2 AA): skip link `#lib-links` stays the first focusable element; `<h1>` profile name; `<nav aria-label>` for links; `:focus-visible` styles with 3:1 contrast; external links announce "(opens in new tab)"; `prefers-reduced-motion` suppresses transforms; profile image alt "Profile photo of [Name]".

## Do Not Change

- Option names `guloli_settings` / `guloli_links` — renaming loses saved data
- `templates/page-simple-bio-links.php` — the path is hardcoded in `GULOLI_Frontend::load_template()`
- Prefixes `GULOLI_` / `guloli_` — collision-avoidance contract
- The `sanitize_*` callbacks in `GULOLI_Settings` — they protect data integrity
- Capability slug `guloli_manage_settings` — stored in the roles table; renaming silently locks out Editors

## Available Agents

All agent definitions live in `.agents/agents/` (source of truth).
`.github/agents/` is a tracked copy of `.agents/agents/` (Copilot needs it there) — never edit files there directly; refresh with `bash .agents/scripts/sync-github-copies.sh`. CI (`ai-config-sync`) fails if they differ.
Each agent has a corresponding Claude Code slash command in `.claude/commands/`.

| Agent | Claude Command | Purpose |
|-------|---------------|---------|
| [code-review.agent.md](.agents/agents/code-review.agent.md) | `/code-review` | WPCS compliance, security, backwards-compat review |
| [wp-security-review.agent.md](.agents/agents/wp-security-review.agent.md) | `/wp-security-review` | XSS, CSRF, SQLi, privilege escalation audit |
| [refine-issue.agent.md](.agents/agents/refine-issue.agent.md) | `/refine-issue` | Add acceptance criteria, technical notes, edge cases to a GitHub issue |

## Available Skills

All skills live in `.agents/skills/` (source of truth).
Each skill has a corresponding Claude Code slash command in `.claude/commands/`.

| Skill | Claude Command | Purpose |
|-------|---------------|---------|
| [gh/SKILL.md](.agents/skills/gh/SKILL.md) | `/gh` | GitHub CLI: issues, PRs, releases, labels |
| [gh-bot/SKILL.md](.agents/skills/gh-bot/SKILL.md) | `/gh-bot` | Open PRs and PR comments as the bot identity `habakuk-ai`; commits and pushes stay `habakuk007` |
| [php-lint/SKILL.md](.agents/skills/php-lint/SKILL.md) | `/php-lint` | PHPCS/PHPCBF for WordPress Coding Standard |
| [translate-de/SKILL.md](.agents/skills/translate-de/SKILL.md) | `/translate-de` | German translation (du/Sie) per WordPress Polyglots Style Guide and Glossary |
| [wp-release/SKILL.md](.agents/skills/wp-release/SKILL.md) | `/wp-release` | Plugin release workflow (version bump → release PR → tag → deploy) |
| [grill-me/SKILL.md](.agents/skills/grill-me/SKILL.md) | `/grill-me` | Stress-test a plan through relentless questions |
| [cross-review/SKILL.md](.agents/skills/cross-review/SKILL.md) | `/cross-review` | Opt-in second-AI review of a pull request: post as bot, evaluate, reply, fix valid findings (one round) |

## Available Instruction Files

All instructions live in `.agents/instructions/` (source of truth).
`.github/instructions/` is a tracked copy of this folder (refresh with `bash .agents/scripts/sync-github-copies.sh`; CI checks it).

| File | Applies To |
|------|-----------|
| [wordpress.instructions.md](.agents/instructions/wordpress.instructions.md) | PHP, JS, CSS, JSON |
| [wpcs.instructions.md](.agents/instructions/wpcs.instructions.md) | `**/*.php` |
| [a11y.instructions.md](.agents/instructions/a11y.instructions.md) | `**` |
| [html-css-style-color-guide.instructions.md](.agents/instructions/html-css-style-color-guide.instructions.md) | `**/*.css`, `**/*.html`, `**/*.js` |
| [readme-txt.instructions.md](.agents/instructions/readme-txt.instructions.md) | `readme.txt`, `changelog.txt` |
| [wordpress-org.instructions.md](.agents/instructions/wordpress-org.instructions.md) | `**` |
| [translation-general.instructions.md](.agents/instructions/translation-general.instructions.md) | `languages/**` |
| [translation-de.instructions.md](.agents/instructions/translation-de.instructions.md) | `languages/*de*.po` |
| [ai-tool-sync.instructions.md](.agents/instructions/ai-tool-sync.instructions.md) | `CLAUDE.md`, `AGENTS.md`, `.github/copilot-instructions.md`, `.agents/**`, `.mcp.json` |
| [agents.instructions.md](.agents/instructions/agents.instructions.md) | `**/*.agent.md` |
| [agent-skills.instructions.md](.agents/instructions/agent-skills.instructions.md) | `**/.agents/skills/**/SKILL.md` |

## MCP Servers

| Server | Transport | Command / URL |
|--------|-----------|---------------|
| playwright-mcp | stdio | `npx @playwright/mcp@latest` |
| markitdown | stdio | `uvx markitdown-mcp@0.0.1a4` |
| github-mcp | http | `https://api.githubcopilot.com/mcp/` |
| microsoft-docs | http | `https://learn.microsoft.com/api/mcp` |
| wporg-mcp-server | stdio | `npx @automattic/mcp-wordpress-remote@latest` (VS Code only) |

Config: `.mcp.json` (Claude Code) · `.vscode/mcp.json` (VS Code). The GitHub server is named `github-mcp` in `.mcp.json` and `github` in `.vscode/mcp.json` on purpose (VS Code signs in to GitHub itself).

github-mcp in Claude Code needs the environment variable `GITHUB_PERSONAL_ACCESS_TOKEN`; without it the server fails to connect. VS Code signs in to GitHub itself.
