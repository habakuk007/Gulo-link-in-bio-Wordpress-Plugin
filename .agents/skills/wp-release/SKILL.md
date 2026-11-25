---
name: wp-release
description: Execute the full release workflow for the Gulo Link-in-Bio WordPress plugin. Use when asked to release a new version, cut a release, bump the version, tag a release, update the changelog, or prepare a new plugin version.
---

# WordPress Plugin Release — Gulo Link-in-Bio

Release workflow: release branch → version bump → changelog → release PR → tag → deploy to WordPress.org.

## When to Use

- Releasing a new plugin version
- Bumping the version after a bug fix or feature
- Preparing a release for WordPress.org SVN submission

## Prerequisites

- `gh` CLI authenticated (`gh auth status`)
- Clean working tree (`git status`)
- All features merged into `develop`; start from an up-to-date `develop`

## Workflow

### Step 1: Determine the new version

```bash
git log --oneline $(git describe --tags --abbrev=0)..HEAD
```

Semantic Versioning, always three parts (`MAJOR.MINOR.PATCH`):
- **Patch** (`0.3.2` → `0.3.3`): bug fixes, no API changes
- **Minor** (`0.3.2` → `0.4.0`): new features, backwards-compatible
- **Major**: breaking changes (option key renames, capability rename, class renames)

### Step 2: Create the release branch

```bash
git switch develop && git pull
git switch -c release/X.Y.Z
```

### Step 3: Bump the version everywhere

Update all of these to `X.Y.Z` (they must be identical):

- `gulo-link-in-bio.php`: the `Version:` header and `define( 'GULOLI_VERSION', 'X.Y.Z' );`
- `readme.txt`: `Stable tag:` (and `Tested up to:` if tested against a newer WordPress version)
- `languages/gulo-link-in-bio.pot`: the `Project-Id-Version` header

### Step 4: Write the changelog entry

```bash
git log $(git describe --tags --abbrev=0)..HEAD --format="%s%n%b"
```

Add the entry at the TOP of `== Changelog ==` in `readme.txt` (and mirror it in `changelog.txt` as the repo does), following `.agents/instructions/readme-txt.instructions.md`:

```
= X.Y.Z =
* Fixed: <description>
* Added: <description>
```

- One bullet per logical change, verb prefix `Fixed`, `Added`, `Changed`, `Removed`, `Improved`
- Omit CI-only or formatting-only changes
- Add an `== Upgrade Notice ==` entry only for breaking changes

### Step 5: Run the checks

```bash
composer run lint:php
composer run test:unit
npm run lint:js
npm run lint:css
```

If strings changed: `composer run make:pot` and `composer run make:mo` (see `/translate-de`). Run `/wp-security-review` on the release diff.

### Step 6: Commit and open the release PR into `develop`

```bash
git add gulo-link-in-bio.php readme.txt changelog.txt languages/
git commit -m "chore(release): prepare version X.Y.Z"
git push -u origin release/X.Y.Z
bash .agents/skills/gh-bot/gh-bot.sh pr release/X.Y.Z "Release X.Y.Z" "<body>"
```

`gh-bot.sh pr` opens PRs against `develop` for every branch except `hotfix/*` and `develop`. The user reviews and merges; never merge as the bot.

### Step 7: Release PR `develop` → `main`

After the release branch is merged into `develop`:

```bash
bash .agents/skills/gh-bot/gh-bot.sh pr develop "Release X.Y.Z" "<body>"
```

The user merges it. Tags are only created on `main`.

### Step 8: Tag on `main`

```bash
git switch main && git pull
git tag X.Y.Z
git push origin X.Y.Z
```

Pushing the tag starts `.github/workflows/deploy.yml` (10up deploy action, environment `Deploy`, reviewer approval required). It publishes the plugin to WordPress.org SVN and the artwork in `.wordpress-org/` to the SVN `assets/` folder. What ships is controlled by `export-ignore` in `.gitattributes`; verify with `git archive --format=zip HEAD -o <tmpdir>/check.zip` that no dev or AI files (`.agents/`, `.claude/`, `CLAUDE.md`, `AGENTS.md`, `opencode.json`, `tests/`) are included.

**Pushing the tag makes the release live on WordPress.org.** Confirm with the user before pushing it. Optionally create the GitHub release afterwards:

```bash
gh release create X.Y.Z --title "vX.Y.Z" --notes-file release-notes.md
```

### Step 9: Validate readme.txt (recommended before the tag)

Open https://wordpress.org/plugins/developers/readme-validator/ and paste the contents of `readme.txt`.

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Tag already exists | `git tag -d X.Y.Z && git push origin :X.Y.Z`, then re-tag (only with the user's approval) |
| `Stable tag` mismatch | `Version:` header, `GULOLI_VERSION` and `Stable tag:` must be identical |
| readme.txt validator errors | Short description ≤ 150 chars, `Tested up to` as `X.Y` |
