---
name: translate-de
description: Translate or review German (de "du", de_DE_formal "Sie") strings of the plugin according to the WordPress German Polyglots Style Guide and Glossary. Use when asked to translate, update or proofread languages/*.po or the .pot file, to check German wording, Du/Sie, glossary terms, placeholders, quotation marks or compound words, or to prepare an import for translate.wordpress.org.
---

# Translate German (de / de_DE_formal)

Rules for German translations of this plugin. Sources: [Style Guide](https://de.wordpress.org/team/handbook/polyglots-team/style-guide/) and [Glossar](https://de.wordpress.org/team/handbook/polyglots-team/das-glossar/) of the German Polyglots team. The complete term list is in [references/glossary.md](references/glossary.md); the full rules are in [references/style-guide.md](references/style-guide.md).

## When to Use

- New or changed strings in `gulo-link-in-bio.pot` need a German translation
- Reviewing existing `languages/gulo-link-in-bio-de*.po`
- Questions about Du/Sie, glossary terms, quotation marks, hyphenation of compounds
- Before an import into translate.wordpress.org

## Prerequisites

- `composer install` (WP-CLI i18n for `composer run make:pot` and `composer run make:mo`)
- Files: `languages/gulo-link-in-bio.pot` (template, English source), `languages/gulo-link-in-bio-de.po` (du), `languages/gulo-link-in-bio-de_DE_formal.po` (Sie)
- Related rules: `.agents/instructions/translation-de.instructions.md` and `translation-general.instructions.md`

## Workflows

### Translate or update strings

1. `composer run make:pot` if the source strings changed; merge the new template into both `.po` files.
2. Translate each untranslated or fuzzy string. First look the terms up in `references/glossary.md`, then apply `references/style-guide.md`.
3. Keep the two variants identical except for the form of address (see below). Do not translate one and copy blindly: Sie-form needs capitalised `Sie`/`Ihr`/`Ihnen` and imperatives like `Geben Sie … an`.
4. Run the checks below, then `composer run make:mo`.

### Review existing translations

Go through the checklist below and report deviations as a list (file, line, current, proposed, rule). Change nothing without being asked.

## Checklist (short form)

1. **Address:** de = lower-case `du`/`dein`; de_DE_formal = `Sie`/`Ihr`. No mixing.
2. **Glossary first:** a glossary term wins over everything else. `custom` is `individuell`, `post type` is `Inhaltstyp`, `GDPR` is `DSGVO`.
3. **Placeholders** (`%s`, `%d`, `%1$s`, `%(name)s`) unchanged; they may move in the sentence. Count them in msgid and msgstr.
4. **Quotation marks** typographic: „…“ (U+201E, U+201C). No `"…"`; nested single: ‚…‘. In `.po` files, a literal `"` must be escaped as `\"`, so prefer the typographic marks.
5. **Non-breaking space** (U+00A0, typed, not as entity) in `z. B.`, `d. h.`, between number and unit (`40 %`, `20 EUR`) and before an en dash used as dash.
6. **Dates and times:** `6. Mai 2015` (no leading zero), `9:00 Uhr`, 24-hour clock. Translate PHP date formats, do not copy them.
7. **Compounds:** more than two parts, or a proper name as a part, get hyphens (`WordPress-Plugin-Entwicklung`, `Open-Source-Lizenz`). Plugin and theme names stay untranslated.
8. **Tone:** short active sentences, no word-for-word translation, no slang (`für das`, not `fürs`; `noch einmal`, not `nochmal`), no personification of software (`erkennt`, `will`, `versucht`).
9. **UI strings** (buttons, menu items, labels): infinitive (`Benutzer hinzufügen`). **Headings:** noun form (`Hinzufügen von Benutzern`); `How to …` becomes `So …` + du-form.
10. **Spelling:** `&` becomes `und`; `e.g.` becomes `z. B.`; `i.e.` becomes `d. h.`; thousands separator `.` from four digits.
11. **Gender:** generic masculine in product texts (`Benutzer`, `Anwender`); where possible rephrase neutrally (`Kontakt`, `Du erhältst …`).
12. **Keep untranslated:** option and setting keys, CSS classes, URLs, and the product name `Gulo Link-in-Bio`.

## Plugin vocabulary

Project decisions that are not in the WordPress glossary (taken over from the existing `.po` files):

| English (msgid) | German |
|-----------------|--------|
| Profile image | Profilbild (UI); the frontend `alt` text "Profile photo of %s" uses `Profilfoto von %s` |
| Bio / Tagline | Bio / Untertitel |
| Background (type, color) | Hintergrund(-typ, -farbe) |
| Gradient | Verlauf (`Verlaufsbeginn`, `Verlaufsende`) |
| Solid color | Volltonfarbe |
| Button style / colors / text | Button-Stil / Button-Farben / Button-Text (compound with hyphen, `Button` stays) |
| Solid (button style) | Ausgefüllt |
| Glass (frosted) | Glas (mattiert) |
| Link(s) | Link(s) (not `Verknüpfung`); "Add Link" = `Link hinzufügen` |
| Imprint | Impressum |
| Privacy Policy | Datenschutzerklärung |
| Appearance | Erscheinungsbild |
| Search Engines | Suchmaschinen |
| (opens in new tab) | (öffnet in neuem Tab) — required accessibility announcement, keep it in every external-link label |
| Skip to links | Zu den Links springen |
| Powered by %s | Bereitgestellt von %s |
| Buy me a coffee | du: `Kauf mir einen Kaffee` / Sie: `Kaufen Sie mir einen Kaffee` |

The only strings that differ between the `de` and `de_DE_formal` files today are the two "Buy me a coffee" strings. Keep it that way unless a new string addresses the user directly. Open point: `Profilbild` (settings UI) vs. `Profilfoto` (frontend alt text) is inconsistent; the alt text must read "Profile photo of [Name]" in English, so decide once whether to unify.

Add new project terms here when they are decided.

## Tooling checks

```bash
# Syntax and placeholders (gettext, if installed)
msgfmt --check-format --statistics -o /dev/null languages/gulo-link-in-bio-de.po
msgfmt --check-format --statistics -o /dev/null languages/gulo-link-in-bio-de_DE_formal.po

# Compile
composer run make:mo
```

If `msgfmt` is not installed, `composer run make:mo` (WP-CLI) reports syntax errors as well but does not check placeholders; count them by hand.

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Term is missing from `references/glossary.md` | The file is a complete export from 2026-11-21. Check GlotPress ([du](https://translate.wordpress.org/locale/de/default/glossary/) / [Sie](https://translate.wordpress.org/locale/de/formal/glossary/)) for newer entries; if the term is not there either, follow the existing translations |
| Source string is unclear or English is wrong | Fix the source string in the PHP code (and the `.pot`), not only the translation |
| Ask a human | German `#polyglots` Slack channel; the Style Guide names it as the place for open questions |

## References

- `references/style-guide.md` — summary of the Style Guide (rules in our own words)
- `references/glossary.md` — complete glossary export (520 entries, 2026-11-21) and how to refresh it
