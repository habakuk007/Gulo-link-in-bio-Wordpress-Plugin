# Contributing to Link in Bio

## Development Setup

### Requirements

- PHP 7.4+
- Composer
- Node.js 18+
- npm
- WP-CLI (for i18n commands — install from [wp-cli.org](https://wp-cli.org/))

### Install dependencies

```bash
composer install
npm install
```

---

## Coding Standards

All PHP must pass WPCS (WordPress Coding Standards) with zero errors:

```bash
composer run lint:php
composer run fix:php   # auto-fix style issues
```

All JS must pass ESLint, and all CSS must pass Stylelint:

```bash
npm run lint:js
npm run lint:css
```

### Key conventions

- **Prefix**: Classes `LIB_`, WordPress options `lib_`, JS globals `libAdmin.*`
- **Text domain**: `link-in-bio` in every i18n call
- **Capability**: Use `lib_manage_settings` (not `manage_options`) for all permission checks
- **Escape on output**: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- **Sanitize on input**: `sanitize_text_field()`, `sanitize_hex_color()`, `esc_url_raw()`
- **No inline JS/CSS** — CSS custom properties are injected via `wp_add_inline_style()`
- **Assets** — enqueued lazily, only when the Link in Bio page template is active

See `CLAUDE.md` for the full rules reference.

---

## Tests

Tests are split into two suites:

### Unit tests (`tests/unit/`)

Use [Brain\Monkey](https://brain-wp.github.io/BrainMonkey/) to stub WordPress functions. No database or WordPress installation required — run them anywhere:

```bash
composer run test:unit
```

- Test file naming: `Test_ClassName.php` containing `class Test_ClassName extends TestCase`.
- Every new public method in `includes/` should have corresponding unit test coverage.
- WordPress functions must be stubbed with `Functions\when()` or `Functions\expect()`.

### Integration tests (`tests/integration/`)

Require a real WordPress environment and database. Two options:

**Option A — local MySQL:**

```bash
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest
WP_TESTS_DIR=/tmp/wordpress-tests-lib composer run test
```

**Option B — Docker via wp-env (requires Docker Desktop):**

```bash
npx @wordpress/env start
composer run test
```

Integration tests run automatically in CI on every push via the `phpunit` GitHub Actions job.

---

## Translations

Translatable strings use `esc_html__()`, `__()`, `esc_attr_e()`, etc. with the domain `link-in-bio`.

After adding or changing user-visible strings:

```bash
# Re-extract strings
composer run make:pot

# Edit the relevant .po file (e.g. languages/link-in-bio-de_DE.po) in Poedit or a text editor

# Recompile all .po → .mo
composer run make:mo
```

Shipped locales: `de_DE`, `fr_FR`, `es_ES`, `uk`.

---

## Commit Messages

Use the conventional format:

```
type: short description (imperative, no period)

Optional body — explain *why*, not *what*.
```

Types: `feat`, `fix`, `docs`, `refactor`, `test`, `build`, `chore`

Examples:
```
feat: add cache purge on settings save
fix: resolve Yoast SEO duplicate canonical
docs: add translation workflow to README
```

---

## Pull Requests

1. Fork the repository and create a feature branch from `main`.
2. Make your changes and ensure all linters and tests pass.
3. Open a PR against `main` with a clear description of what changed and why.
4. Link any related issue in the PR description.

PRs that introduce new options must:
- Add a sanitize callback in `LIB_Settings`
- Add the key to the settings table in `README.md`
- Update `CLAUDE.md` if the key is important for AI context

---

## Release Process

1. Update the version string in **all three places**:
   - `link-in-bio.php` — plugin header `Version:` and `LIB_VERSION` constant
   - `package.json` — `"version"` field
   - `readme.txt` — `Stable tag:` header (use a plain version number, e.g. `1.0.0`)
2. Add a `== Changelog ==` entry in `readme.txt`.
3. Add an `== Upgrade Notice ==` entry if the update needs user action.
4. Run all linters and tests (`composer run lint:php`, `composer run test:unit`, `npm run lint:js`, `npm run lint:css`).
5. Run `composer run make:pot` and update PO files if strings changed.
6. Run `composer run make:mo` to recompile MO files.
7. Commit with message: `chore: release X.Y.Z`
8. Push the commit, then push the version tag:
   ```bash
   git tag 1.0.0
   git push origin 1.0.0
   ```
9. The `deploy.yml` GitHub Actions workflow runs automatically and deploys to WordPress.org SVN.
10. Build the distributable ZIP for GitHub releases: `composer run package`

---

## WordPress.org Submission (first time only)

Before the automated deploy can work, the plugin must be manually submitted for review:

1. Ensure you have a [WordPress.org account](https://login.wordpress.org/register).
2. Go to [wordpress.org/plugins/developers/add/](https://wordpress.org/plugins/developers/add/).
3. Submit the plugin ZIP with a brief description.
4. Wait for the review team to approve the submission (typically 1–2 weeks).
5. Once approved, you receive an SVN repository URL and the plugin slug is confirmed.
6. Add your WordPress.org credentials as GitHub repository secrets:
   - `SVN_USERNAME` — your WordPress.org username
   - `SVN_PASSWORD` — your WordPress.org password or [application password](https://make.wordpress.org/core/2020/10/23/application-passwords-integration-guide/)
7. Make sure the `SLUG` in `.github/workflows/deploy.yml` matches the approved slug.
8. Complete the plugin artwork (see `.wordpress-org/README.md`):
   - Export `icon-128x128.png` and `icon-256x256.png` from `icon.svg`
   - Create `banner-772x250.png` and `banner-1544x500.png`
   - Capture screenshots `screenshot-1.png` through `screenshot-5.png`
9. Push the first version tag to trigger the deploy workflow.

### Pre-submission checklist

- [ ] `readme.txt` is valid (check at [wordpress.org/plugins/developers/readme-validator/](https://wordpress.org/plugins/developers/readme-validator/))
- [ ] `Stable tag` in `readme.txt` matches the version in `link-in-bio.php`
- [ ] All strings use `link-in-bio` text domain
- [ ] No calls to external services without user consent
- [ ] No obfuscated code, no minified code without source
- [ ] Plugin uses WordPress's bundled libraries (jQuery, etc.) — does not bundle its own
- [ ] All linters pass: `composer run lint:php`, `npm run lint:js`, `npm run lint:css`
- [ ] Plugin deactivation removes the custom capability cleanly (via `LIB_Plugin::deactivate()`)
- [ ] Plugin deletion removes all options and capabilities cleanly (via `uninstall.php`)
- [ ] Screenshots and banner artwork are in `.wordpress-org/`
- [ ] `readme.txt` `Screenshots` section matches the number of screenshot files
