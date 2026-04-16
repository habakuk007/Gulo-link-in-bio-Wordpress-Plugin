# WordPress.org Plugin Assets

This directory maps to the `/assets/` folder of the plugin's WordPress.org SVN repository.
Files here are **not** included in the plugin ZIP — they appear only on the plugin's directory page.

The deployment workflow (`.github/workflows/deploy.yml`) copies this directory to SVN `/assets/`
automatically when a version tag is pushed.

---

## Icon

| File | Dimensions | Status |
|------|-----------|--------|
| `icon.svg` | Scalable (256×256 viewBox) | **Done** |
| `icon-128x128.png` | 128 × 128 px | **Needs export** |
| `icon-256x256.png` | 256 × 256 px | **Needs export** |

### How to export the PNG icons

Open `icon.svg` in a vector editor (Inkscape, Figma, Illustrator) or use a browser/CLI:

```bash
# Using Inkscape (CLI):
inkscape icon.svg --export-png=icon-128x128.png --export-width=128 --export-height=128
inkscape icon.svg --export-png=icon-256x256.png --export-width=256 --export-height=256

# Using rsvg-convert (librsvg):
rsvg-convert -w 128 -h 128 icon.svg -o icon-128x128.png
rsvg-convert -w 256 -h 256 icon.svg -o icon-256x256.png

# Using ImageMagick (requires librsvg delegate):
convert -background none icon.svg -resize 128x128 icon-128x128.png
convert -background none icon.svg -resize 256x256 icon-256x256.png
```

WordPress prefers SVG + PNG fallback. The SVG alone is sufficient, but adding PNGs
ensures the icon displays correctly in older browsers and the Plugins page email digest.

---

## Banner

The banner appears at the top of the plugin's directory page.

| File | Dimensions | Notes |
|------|-----------|-------|
| `banner-772x250.png` (or `.jpg`) | 772 × 250 px | Standard — **required** |
| `banner-1544x500.png` (or `.jpg`) | 1544 × 500 px | Retina/HiDPI — **recommended** |

### Design guidelines

- Max file size: 4 MB per banner
- Format: PNG (recommended for clean edges) or JPG
- Background: use the plugin's gradient `#667eea` → `#764ba2` (top-left to bottom-right)
- Include the plugin name "Link in Bio" in a clean sans-serif font
- White or light text for contrast
- Optionally show a stylized screenshot of the frontend profile page on the right side
- Leave a safe zone of ~40 px on all edges for responsive cropping

### Content suggestion

```
Left side:
  - Plugin name: "Link in Bio" (large, white, bold)
  - Tagline: "Your Linktree — inside WordPress." (smaller, white)

Right side:
  - Mockup / screenshot of the frontend profile page
  - Or the icon enlarged with a soft glow
```

---

## Screenshots

Screenshots appear in the **Screenshots** tab on the plugin's directory page.
File names must be lowercase and match the numbering in `readme.txt`.

| File | Shows | Status |
|------|-------|--------|
| `screenshot-1.png` | Settings page — Profile and Page selection | **Needs capture** |
| `screenshot-2.png` | Settings page — Appearance (colors, gradient, button style) | **Needs capture** |
| `screenshot-3.png` | Settings page — Links manager (drag rows) | **Needs capture** |
| `screenshot-4.png` | Live frontend profile page (desktop) | **Needs capture** |
| `screenshot-5.png` | Live frontend profile page (mobile) | **Needs capture** |

### Screenshot requirements

- Format: PNG or JPG (lowercase extension required by WordPress.org)
- Max size: 10 MB per screenshot
- Recommended width: 1200–1600 px for retina displays
- Capture at 100% browser zoom; use a clean/demo profile
- No personal data in the screenshots

### How to capture

1. Install the plugin on a local or staging WordPress site.
2. Set up a demo profile (name: "Jane Doe", bio: "Creator & Developer", some links).
3. Use your browser's screenshot tool or a tool like [GoFullPage](https://gofullpage.com/)
   for full-page captures.
4. Crop and save as PNG to this directory.

---

## Localized assets (optional)

WordPress.org supports locale-specific banners and screenshots:

```
banner-772x250-de.png      ← German banner
screenshot-1-de.png        ← German screenshot 1
```

Not required for initial submission. Add later if needed.
