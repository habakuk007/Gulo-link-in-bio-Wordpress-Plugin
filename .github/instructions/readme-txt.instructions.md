---
applyTo: 'readme.txt,changelog.txt'
description: 'WordPress.org readme.txt format rules: header fields, section structure, changelog authoring from git log, Upgrade Notice limits, version sync, and file-size management.'
---

# WordPress.org `readme.txt` Standards

Rules for authoring and maintaining `readme.txt` in this plugin. The file controls the
plugin's listing on `wordpress.org/plugins/simple-bio-links` and drives automatic updates.
Reference: [How Your readme.txt Works](https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/)

---

## 1) Header Fields

The header block is the first section of `readme.txt`. Every field has strict requirements.

### `Stable tag` — CRITICAL

- Must always equal **both** the `Version:` field in `simple-bio-links.php` **and** the
  `GULOLI_VERSION` constant defined in the same file.
- Numbers and periods only (SemVer: `MAJOR.MINOR.PATCH`). Never use `trunk`.
- WordPress.org reads the `Stable tag` to determine which `/tags/` directory to serve.
  A mismatch between `Stable tag` and the PHP header version causes the wrong version
  to appear on the download button.

### Short description (the line after the last header field)

- Maximum **150 characters**. No Markdown markup. Will be hard-truncated by the directory.
- Describes what the plugin does in plain language; not a marketing tagline.

### `Tested up to`

- Major.minor format only — e.g. `6.9`, never `WP 6.9` or `6.9.1`.
- Update this field each release cycle when testing against a newer WordPress version.
- The directory automatically appends the minor patch; plugins should not break on minor updates.

### `Tags`

- 1–5 comma-separated lowercase terms.
- Do not use competitor brand names (e.g. Linktree, Beacons) as tags — this violates WordPress.org guidelines.
- Do not use tags that are unique to this plugin alone (they won't appear in tag browsing).

### `Contributors`

- Comma-separated list of **WordPress.org usernames** — case-sensitive, no spaces around commas.
- Do not use email addresses, GitHub handles, or display names here.

### `Requires at least` and `Requires PHP`

- Since WordPress 5.8 these values are parsed from the plugin's main PHP file header, not from `readme.txt`.
- Keep both files in sync, but treat `simple-bio-links.php` as the authoritative source.
- Format: numbers only — `6.0`, `7.4`. No "WP" or "PHP" prefix.

---

## 2) Required and Recommended Sections

### Required
- `== Description ==` — what the plugin does; written for end users, not developers.
- `== Changelog ==` — version history; see Section 4 for authoring rules.

### Recommended
- `== Installation ==` — omit only if the plugin has zero custom install steps.
- `== Frequently Asked Questions ==` — address real support questions.
- `== Screenshots ==` — numbered captions that must match screenshot files uploaded to the SVN
  `/assets/` directory. Never reference a screenshot number that does not have a corresponding file.
- `== Upgrade Notice ==` — per-version notes for users; see Section 5.

### Custom sections

Custom sections (e.g. `== Credits ==`) are allowed but use them sparingly. Users expect the
standard layout; unexpected sections are often skipped.

---

## 3) Markdown

`readme.txt` uses a subset of Markdown. The following work reliably:

- `**bold**`, `*italic*`
- `` `inline code` ``
- Unordered lists with `*` or `-`
- Numbered lists
- `[Link text](URL)`
- YouTube / Vimeo URLs on a line by themselves (auto-embedded)

Do not use HTML tags, definition lists, or tables — they are not reliably rendered.

---

## 4) Changelog Authoring Rules

### Order
Entries are in **descending version order** — newest version at the top.

### Always derive from `git log`

Never write `* Version bump` as the sole changelog entry. Before writing a new entry, run:

```
git log {previous_tag}..HEAD --format="%s%n%b"
```

Read every commit message and body, then summarise the **logical changes** — not the file names.

### One bullet per logical change

Group related file edits into a single bullet that describes the user- or developer-visible outcome.

- BAD: `* Updated includes/class-guloli-admin.php and includes/class-guloli-settings.php`
- GOOD: `* Fixed background_type and button_style sanitization: now uses strict allowlist`

### Verb prefix

Start each bullet with one of: `Added` / `Fixed` / `Changed` / `Removed` / `Updated` / `Improved`.

### What to include

- Functional changes visible to end users (new options, UI changes, bug fixes)
- Changes visible to developers (renamed classes, constants, option keys, capabilities)
- Security fixes
- Breaking changes (option renames, capability renames, template variable renames)

### What to omit

- Pure CI / GitHub Actions changes with no user or developer impact
- Whitespace / formatting fixes with no functional effect
- Internal test-only changes (unless they affect the public test API)

### File size management

WordPress.org flags readmes larger than 10 KB. When the file approaches that limit:

1. Create `changelog.txt` in the plugin root.
2. Move all entries older than the two most recent releases into `changelog.txt`.
3. Add a note at the bottom of `== Changelog ==`: `See changelog.txt for older entries.`

---

## 5) Upgrade Notice Rules

- Maximum **150 characters** per version entry. The directory truncates longer notices.
- Only add an entry when users need to **take action** or must be **aware of a breaking change**
  (renamed options, capability changes, removed shortcodes, template changes).
- Skip releases that contain only internal fixes, test updates, or CI changes.
- Phrasing: one sentence, imperative or informational, e.g.:
  `Option keys renamed from gulo_ to guloli_. Re-save settings after upgrading.`

---

## 6) Version Bump Checklist

Follow these steps in order every time the version number changes:

1. Update `Version:` in the plugin header inside `simple-bio-links.php`.
2. Update `define( 'GULOLI_VERSION', '...' )` in `simple-bio-links.php` to the same value.
3. Update `Stable tag:` in `readme.txt` to the same value.
4. Add a new `= X.Y.Z =` entry at the top of `== Changelog ==` derived from `git log`.
5. Add or update the `== Upgrade Notice ==` entry if the release contains breaking changes.
6. Run `composer run package` to build `simple-bio-links-{version}.zip`.
7. Commit with message `chore: release X.Y.Z` and tag the commit `X.Y.Z`.

---

## 7) Validation

Before every release, validate `readme.txt` at:
`https://wordpress.org/plugins/developers/readme-validator/`

All fields must pass without errors. Common failures:
- `Stable tag` does not match the plugin PHP header version
- Short description exceeds 150 characters
- `Tested up to` contains a patch version or "WP" prefix
- Screenshot captions reference numbers with no corresponding SVN asset file
