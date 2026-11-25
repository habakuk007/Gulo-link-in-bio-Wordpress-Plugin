---
name: php-lint
description: Run PHPCS and PHPCBF to lint and auto-fix PHP files against the WordPress Coding Standard. Use when asked to lint PHP, check coding standards, fix style issues, run phpcs, run phpcbf, check WPCS compliance, or validate PHP code quality.
---

# PHP Lint — PHPCS / PHPCBF for WordPress

Lint and auto-fix PHP files against the WordPress Coding Standard (WPCS) and check PHP compatibility (PHP 7.4+).

The configuration lives in the repository:

- `composer.json` — dev tooling (PHPCS, WPCS, PHPCompatibility) and the scripts `lint:php` and `fix:php`. Not part of the release.
- `phpcs.xml` — rulesets `WordPress`, `WordPress-Docs`, `WordPress-Extra` + `PHPCompatibility`, `testVersion` `7.4-`, text domain `gulo-link-in-bio`. Scans the whole repo; `vendor/`, `node_modules/`, `bin/`, `assets/js/`, `assets/css/` and `tests/bootstrap.php` are excluded.

## When to Use

- Before committing PHP changes
- Checking if code passes WordPress.org plugin review requirements
- Auto-fixing whitespace, alignment, and formatting issues
- Verifying a file is WPCS-compliant
- Checking that code still runs on PHP 7.4 and newer (part of the same ruleset)

## Prerequisites

Install the dev tooling once (creates `vendor/`, which is git-ignored):

```bash
composer install
```

Check the installation:

```bash
vendor/bin/phpcs --version
vendor/bin/phpcs -i   # should list "WordPress" and "PHPCompatibility"
```

Use the project's `vendor/bin/phpcs`, not a globally installed `phpcs` — a global install may lack PHPCompatibility or have other versions.

## Workflows

### Coding standard, whole plugin

```bash
# Full ruleset from phpcs.xml (also checks PHP 7.4+ compatibility)
composer run lint:php

# Summary per file only
composer run lint:php -- --report=summary
```

CI runs the same ruleset on PHP 7.4–8.3; new and changed code must be clean.

### Lint a single file or directory

Paths on the command line replace the file list from `phpcs.xml`; the ruleset still applies.

```bash
vendor/bin/phpcs gulo-link-in-bio.php
vendor/bin/phpcs includes/
```

### Auto-fix fixable violations

```bash
# All plugin PHP files
composer run fix:php

# A single file
vendor/bin/phpcbf gulo-link-in-bio.php
```

PHPCBF fixes whitespace, alignment, bracket placement, and most formatting issues. It **cannot** fix logic issues like missing escaping or nonces — those require manual fixes. Review the diff afterwards and keep mass auto-fixes in their own commit.

### Lint only specific rules

```bash
# Check only security-related sniffs
vendor/bin/phpcs --sniffs=WordPress.Security.EscapeOutput,WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput
```

### Write a report file

```bash
vendor/bin/phpcs --report=full --report-file=phpcs-report.txt
```

Do not commit the report file.

## Common Violations in This Plugin

| Violation | Sniff | Fix |
|-----------|-------|-----|
| Spaces instead of tabs, spacing inside parentheses, trailing whitespace | `Generic.WhiteSpace.*`, `WordPress.WhiteSpace.*` | `composer run fix:php` |
| Missing or incomplete doc comments | `Squiz.Commenting.*`, `WordPress-Docs` | Write them by hand |
| Unescaped output | `WordPress.Security.EscapeOutput` | `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()` |
| Request data without nonce check or sanitizing | `WordPress.Security.NonceVerification`, `WordPress.Security.ValidatedSanitizedInput` | `wp_unslash()` + sanitize; nonce via Settings API for writes |
| Non-prefixed globals in templates | `WordPress.NamingConventions.PrefixAllGlobals` | Prefix with `guloli_`; templates included inside a method need an inline `phpcs:disable` with a reason |
| Wrong text domain | `WordPress.WP.I18n.TextDomainMismatch` | Use `gulo-link-in-bio` |

Never rename classes, options, the capability slug or the template file to satisfy a sniff (see "What NOT to Change" in `CLAUDE.md`).

## Troubleshooting

| Problem | Solution |
|---------|----------|
| `ERROR: the "WordPress" coding standard is not installed` | Run `composer install`; use `vendor/bin/phpcs`, not a global `phpcs` |
| `ERROR: Referenced sniff "PHPCompatibility" does not exist` | Same: `composer install`, then `vendor/bin/phpcs -i` |
| `phpcs: command not found` | Run `composer install` and call `vendor/bin/phpcs` or the Composer scripts |
| Many violations after a refactor | Run `composer run fix:php` for the auto-fixable ones, then review the remainder |
| `Squiz.PHP.CommentedOutCode` false positives | Add `// phpcs:ignore` with the sniff name inline if intentional |
