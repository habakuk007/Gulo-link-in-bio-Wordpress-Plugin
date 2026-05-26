#!/usr/bin/env bash
# Builds gulo-link-in-bio.zip for WordPress upload.
# Usage: composer run package
#
# The zip contains a single top-level folder named "gulo-link-in-bio/" so that
# WordPress installs it to wp-content/plugins/gulo-link-in-bio/.
# To update an existing install: Plugins → Add New → Upload Plugin,
# then click "Replace current with uploaded" (NOT "Install Now").
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
VERSION=$(awk '/\* Version:/{print $NF; exit}' "$ROOT/gulo-link-in-bio.php")
ZIP="$ROOT/gulo-link-in-bio.zip"

# Convert to Windows path for PowerShell
WIN_ROOT="$(cygpath -w "$ROOT" 2>/dev/null || echo "$ROOT")"
WIN_ZIP="$(cygpath -w "$ZIP" 2>/dev/null || echo "$ZIP")"

powershell -NoProfile -Command "
  \$src  = '$WIN_ROOT'
  \$zip  = '$WIN_ZIP'

  if (Test-Path \$zip) { Remove-Item \$zip }

  \$tmp  = Join-Path ([System.IO.Path]::GetTempPath()) ('gulo-zip-' + [System.IO.Path]::GetRandomFileName())
  \$plug = Join-Path \$tmp 'gulo-link-in-bio'
  New-Item -ItemType Directory -Path \$plug | Out-Null

  # Individual root files
  foreach (\$f in @('gulo-link-in-bio.php', 'uninstall.php', 'readme.txt', 'LICENSE')) {
    Copy-Item (Join-Path \$src \$f) \$plug
  }

  # Directories (recursive)
  foreach (\$d in @('includes', 'templates', 'assets', 'languages')) {
    Copy-Item (Join-Path \$src \$d) (Join-Path \$plug \$d) -Recurse
  }

  Compress-Archive -Path \$plug -DestinationPath \$zip

  Remove-Item \$tmp -Recurse -Force

  Add-Type -AssemblyName System.IO.Compression.FileSystem
  \$z = [System.IO.Compression.ZipFile]::OpenRead(\$zip)
  Write-Host 'Zip contents (first 10 entries):'
  \$z.Entries | Select-Object -First 10 | ForEach-Object { Write-Host ('  ' + \$_.FullName) }
  \$total = \$z.Entries.Count
  \$z.Dispose()
  \$kb = [math]::Round((Get-Item \$zip).Length / 1KB, 1)
  Write-Host \"... \$total entries total\"
  Write-Host \"Built: \$zip (\$kb KB)\"
"
