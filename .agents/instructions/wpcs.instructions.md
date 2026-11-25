---
applyTo: '**/*.php'
description: 'WordPress Coding Standards (WPCS) reference for this plugin: how to run checks, what phpcs.xml enforces, per-file usage, inline suppression, and common error interpretation.'
---

# WordPress Coding Standards (WPCS) — Project Reference

**Authority:** https://github.com/WordPress/WordPress-Coding-Standards

WPCS is enforced via PHPCS on every PHP file in this plugin. JS is linted by ESLint; CSS by Stylelint.
This file is the AI-tool reference for running checks, reading errors, and suppressing rules correctly.

---

## 1) Setup Verification

```bash
# Confirm PHPCS can see the installed standards
composer exec phpcs -- --config-show | grep installed_paths
# Expected output includes paths to WordPress-Coding-Standards and PHPCompatibility
```

If `installed_paths` is empty, run `composer install` — the
`dealerdirect/phpcodesniffer-composer-installer` package registers standards automatically.

---

## 2) Run Commands

```bash
# Lint all PHP files (verbose progress — one dot per file)
composer run lint:php

# Auto-fix all fixable violations
composer run fix:php
```

`phpcbf` fixes a subset of violations automatically (whitespace, indentation, brace placement,
blank lines). It cannot fix naming conventions, missing DocBlocks, escaping calls, or logic errors.
Always re-run `lint:php` after `fix:php` to catch remaining non-fixable violations.

---

## 3) Check a Single File During Development

```bash
# Lint one file and show sniff codes (needed for suppression)
vendor/bin/phpcs --standard=phpcs.xml --report=full -s path/to/file.php

# Auto-fix one file
vendor/bin/phpcbf --standard=phpcs.xml path/to/file.php
```

The `-s` flag prints the sniff code (e.g. `WordPress.Security.EscapeOutput.OutputNotEscaped`)
beside each violation — required when writing inline suppressions.

---

## 4) What `phpcs.xml` Enforces in This Project

| Rule / Config | Value |
|---|---|
| Rulesets | `WordPress`, `WordPress-Docs`, `WordPress-Extra` |
| PHP compatibility | `PHPCompatibility` — `testVersion 7.4-` (PHP 7.4 and above) |
| Text domain | `gulo-link-in-bio` — any other value in `__()` / `_e()` etc. is an error |
| Custom capability | `guloli_manage_settings` — registered so `WordPress.WP.Capabilities` does not flag it |
| Global prefix | `guloli_` (functions, hooks, global vars, constants) / `GULOLI_` (class names) — both accepted by `PrefixAllGlobals` via case-insensitive matching |
| Line length | 200 characters (hard limit; `Generic.Files.LineLength`) |
| **Excluded paths** | `vendor/*`, `node_modules/*`, `tests/bootstrap.php`, `bin/*`, `assets/js/*`, `assets/css/*` |

**Rulesets in plain terms:**
- `WordPress` — core rules: escaping, sanitization, nonces, SQL prepare, `$_REQUEST` ban, spacing, naming.
- `WordPress-Docs` — DocBlocks required for functions, classes, and file headers.
- `WordPress-Extra` — stricter checks: Yoda conditions, array alignment, deprecated function detection.
- `PHPCompatibility` — flags constructs that do not work in PHP 7.4 (e.g. named arguments, enums, fibers).

---

## 5) Common Errors and How to Fix Them

| Error code | Meaning | Fix |
|---|---|---|
| `WordPress.Security.EscapeOutput.OutputNotEscaped` | Output not escaped | Wrap with `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`, etc. |
| `WordPress.Security.NonceVerification.Missing` | Form/AJAX handler missing nonce check | Add `check_admin_referer()` or `check_ajax_referer()` |
| `WordPress.WP.I18n.NonSingularStringLiteralDomain` | Variable used as text domain | Hard-code `'gulo-link-in-bio'` — never use a variable |
| `WordPress.WP.I18n.MismatchedTextDomain` | Wrong text domain string | Use `'gulo-link-in-bio'` in every i18n call |
| `WordPress.DB.PreparedSQL.NotPrepared` | Raw SQL without `$wpdb->prepare()` | Use `$wpdb->prepare()` with `%s`/`%d`/`%f` placeholders |
| `WordPress.NamingConventions.PrefixAllGlobals.*` | Function, class, or hook missing the required prefix | Functions, hooks, global vars, constants: rename to start with `guloli_`; class names: use `GULOLI_` |
| `WordPress.PHP.YodaConditions.NotYoda` | Non-Yoda comparison (`$x === 'foo'`) | Invert to `'foo' === $x` |
| `Squiz.Commenting.FunctionComment.Missing` | Missing function DocBlock | Add `/** @param ... @return ... */` above the function |
| `PHPCompatibility.FunctionUse.*` | PHP 8+ syntax used | Replace with PHP 7.4-compatible alternative |
| `Generic.Files.LineLength.TooLong` | Line exceeds 200 characters | Break the line; extract to variable or split string |

---

## 6) Inline Suppression

Use suppressions sparingly — only when a rule genuinely does not apply to the specific context.

```php
// Suppress a single sniff on the next line
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is pre-escaped by wp_kses_post earlier.
echo $trusted_html;

// Suppress multiple sniffs on one line
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- Table name cannot be parameterised; value is internal constant.
$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}guloli_cache" );

// Suppress a block — always add a reason and keep it as narrow as possible
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Read-only GET parameter; no state mutation.
$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
// phpcs:enable WordPress.Security.NonceVerification.Missing
```

**Rules for suppressions:**
- Always include a reason after ` -- ` on the same comment line.
- Prefer `phpcs:ignore` (one line) over `phpcs:disable` / `phpcs:enable` blocks.
- Never suppress `WordPress.Security.EscapeOutput` in template files — fix the escaping instead.
- Never suppress `WordPress.DB.PreparedSQL` — use `$wpdb->prepare()` correctly.

---

## 7) DocBlock Requirements (`WordPress-Docs`)

Every public function and class requires a DocBlock. Minimum required tags:

```php
/**
 * Short description of what this function does.
 *
 * @param string $key     Option key to retrieve.
 * @param mixed  $default Default value when key is not set.
 * @return mixed The option value or $default.
 */
function guloli_get_option( string $key, $default = null ) { ... }
```

File-level DocBlocks are required at the top of each PHP file (after the `defined( 'ABSPATH' ) || exit;` guard):

```php
/**
 * Admin settings page for the Gulo Link-in-Bio plugin.
 *
 * @package Gulo_Link_In_Bio
 */
```

---

## 8) Pre-Commit Checklist

- [ ] `composer run lint:php` exits with zero errors
- [ ] All new functions and classes have DocBlocks
- [ ] All output is wrapped in the correct escape function for its context
- [ ] Text domain is the hard-coded string `'gulo-link-in-bio'` in every i18n call
- [ ] New globals, hooks, and functions are prefixed `guloli_`
- [ ] No line exceeds 200 characters
- [ ] Any `phpcs:ignore` or `phpcs:disable` includes a reason after ` -- `
