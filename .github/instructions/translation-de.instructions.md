---
applyTo: 'languages/*-de.po, languages/*de_DE*.po, languages/*de_AT*.po, languages/*de_CH*.po, languages/*de_DE_formal*.po'
description: 'German WordPress translation rules — style guide, glossary, and terminology for de, de_DE, de_AT, de_CH, and de_DE_formal locales.'
---

# German WordPress Translation (Deutsch)

Use these rules for all German locale translation files (`de`, `de_DE`, `de_AT`, `de_CH`, `de_DE_formal`). They are based on the official [German WordPress Style Guide](https://de.wordpress.org/team/handbook/polyglots-team/style-guide/) and the translate.wordpress.org glossaries.

## Glossary

Always look up a term in the official glossaries before choosing a German equivalent:

- **Default (du-Form, `de` and `de_DE`)**: https://translate.wordpress.org/locale/de/default/glossary/
- **Formal (Sie-Form, `de_DE_formal`)**: https://translate.wordpress.org/locale/de/formal/glossary/
- **Reference article**: https://de.wordpress.org/team/handbook/polyglots-team/das-glossar/

A term present in the glossary must use the glossary translation — do not invent alternatives.

## Tone & Formality

- `de`, `de_DE`, `de_AT`, `de_CH` — informal **„du"** (lowercase, even mid-sentence)
- `de_DE_formal` — formal **„Sie"** (always capitalized)
- Write for non-technical users; clarity over technical jargon
- Avoid dialect, colloquialisms, idioms, and humour that does not translate
- Expand contracted forms: "fürs" → "für das", "vorm" → "vor dem", "drauf" → "darauf"

## Brand Names

- **WordPress**, **WordPress.org**, and **WordPress.com** are never translated; keep exactly as written (they name different products)
- Plugin and theme names are brand names — do not translate them
- Terms that stay in English per glossary: Dashboard, Customizer, Jetpack, Inserter, Appender, Bookmarklet, Drag-and-drop

## Imperatives & UI Labels

- Preserve English imperative forms: "Click" → "Klicke" (du) / "Klicken Sie" (Sie)
- UI button labels use **infinitives**: "Add User" → "Benutzer hinzufügen"
- Avoid bare infinitives where an imperative is expected: use "Drück F1", not "F1 drücken"
- Software must not be anthropomorphised: it does not "want", "try", or "know" — it "performs", "executes", or "checks"

## Headings & Titles

- Titles must be concise; do not pad length to match the source
- No trailing period in a title
- No parenthetical asides in a title
- Gerund / infinitive constructions → nominalized verb without article: "Adding Fields" → "Hinzufügen von Feldern"
- "About X" → "Informationen zu X"
- "How to / To do X" → "So [tust du / tun Sie] X"
- Link text for untranslated English documents: render title in German and append "(engl.)"

## Orthography

### Quotation Marks

Use German typographic double quotes: **„…"** (U+201E opening, U+201C closing).

| OS | Opening „ | Closing " |
|---|---|---|
| Windows | Alt + 0132 | Alt + 0147 |
| macOS | Alt + ^ | Alt + Shift + ^ |

Do not use straight quotes `"..."` in translated strings.

### Punctuation

- Replace `&` with **„und"** in running text; retain `&` only inside HTML attributes and code
- Em dash (—): precede with a protected space (non-breaking space); do not substitute a hyphen
- Protected space between number and unit or percent sign: "40 %", "25 kg", "1 km"

### Numbers & Currency

- Thousands separator: full stop — "1.000.000"
- Decimal separator: comma — "3,14"
- Currency symbol follows the amount with a protected space: "9,99 €"
- Numbers up to twelve may be written out in body text; use digits before abbreviations ("1 km", "20 EUR")
- Do not convert Anglo-American measurements unless the source string already does so

### Dates & Times

- Written format: DD.MM.YYYY with no leading zero on the day — "13. Juni 2013"
- 24-hour clock with colon: "21:00 Uhr"
- PHP date format `M j, Y @ G:i` becomes `j. M Y, G:i` (the `@` converts to a comma)
- Do not literally translate format tokens like `MMMM`, `dddd`, or `LT` — rearrange them for German locale conventions

### Compound Words

- German compounds are written as **one word** or **hyphenated** — never as two separate words
- Hyphenate mixed German/English compounds: "Block-Editor", "Action-Hook", "Filter-Hook", "Header-Bild"
- Use Duden (https://www.duden.de) or LEO (https://www.leo.org) for uncertain compound formation
- Separate words are the exception, not the rule ("Die Getrenntschreibung von Wörtern ist der Normalfall" applies in reverse: when in doubt, write as one word or hyphenate)

### Lists

Maintain a consistent style within a single list:

- **Complete sentences**: capital first letter + trailing period on every item
- **Incomplete entries**: capital first letter, no trailing period on any item

Never mix the two styles in the same list.

## Gender & Inclusive Language

- Long-form documentation: use gender-inclusive language (e.g., "Beteiligte" instead of "Beteiligte/Beteiligter")
- UI and product strings: generic masculine is acceptable ("Autor", "Administrator") where it keeps the label short
- Prefer neutral forms where they are natural and do not add length ("Mitwirkende" over "Mitwirkender/Mitwirkende")

## Abbreviations & Acronyms

- Common abbreviations use protected spaces between letters: "z. B." (e.g.), "d. h." (i.e.), "u. a." (among others)
- First occurrence of a technical acronym: write out the full term with the acronym in parentheses; subsequent occurrences: acronym alone
- Technical acronyms with no established German equivalent stay in English; optionally expand on first use

## Key Glossary Terms

Representative entries — consult the full glossaries linked above for the complete list.

| English | German | Notes |
|---|---|---|
| account | Konto | — |
| activate / deactivate | aktivieren / deaktivieren | — |
| admin bar | Adminleiste | — |
| Appearance | Design | UI menu item |
| capability | Berechtigung | user-role context |
| category | Kategorie | — |
| comment (noun) | Kommentar | — |
| comment (verb) | kommentieren | — |
| custom | individuell | **not** "benutzerdefiniert" |
| Custom Post Type | Individueller Inhaltstyp | — |
| dashboard | Dashboard | not translated |
| default | Standard | — |
| delete | löschen | — |
| developer | Entwickler | gender-neutral forms acceptable |
| draft | Entwurf | — |
| editor (tool) | Editor | e.g. Block Editor |
| editor (role) | Redakteur | user role |
| email | E-Mail | always hyphenated, capital M |
| excerpt | Textauszug | — |
| feature (noun) | Funktion | — |
| featured image | Beitragsbild | — |
| filter | Filter | singular and plural |
| footer (UI) | Footer | general UI usage |
| footer (table) | Fußzeile | tables and documents only |
| GDPR | DSGVO | German statutory abbreviation |
| GMT | UTC | always use UTC |
| header (UI) | Header | general UI usage |
| header (table) | Kopfzeile | tables and documents only |
| hover | bei Mauszeigerkontakt | — |
| link | Link | not translated |
| pattern | Vorlage | content-design context |
| tag (taxonomy) | Schlagwort | — |
| template | Vorlage | layout/functionality context |
| update (noun/verb) | aktualisieren / Aktualisierung | — |
| user | Benutzer | — |

*Source: [German Style Guide](https://de.wordpress.org/team/handbook/polyglots-team/style-guide/) · [Das Glossar](https://de.wordpress.org/team/handbook/polyglots-team/das-glossar/) · [Default Glossary](https://translate.wordpress.org/locale/de/default/glossary/) · [Formal Glossary](https://translate.wordpress.org/locale/de/formal/glossary/)*
