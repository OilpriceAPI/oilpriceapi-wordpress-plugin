#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
VERSION="$(sed -n 's/^ \* Version: //p' "$ROOT_DIR/oilpriceapi-widget.php")"
DIST_DIR="$ROOT_DIR/dist"
PLUGIN_DIR="$DIST_DIR/oilpriceapi-widget"
ARCHIVE="$DIST_DIR/oilpriceapi-widget-$VERSION.zip"

if [[ -e "$DIST_DIR" ]]; then
  echo "Refusing to overwrite $DIST_DIR. Remove the generated directory before rebuilding." >&2
  exit 1
fi

mkdir -p "$PLUGIN_DIR/assets"
cp "$ROOT_DIR/LICENSE" "$ROOT_DIR/readme.txt" "$ROOT_DIR/oilpriceapi-widget.php" "$PLUGIN_DIR/"
cp -R "$ROOT_DIR/admin" "$ROOT_DIR/blocks" "$ROOT_DIR/includes" "$PLUGIN_DIR/"
cp -R "$ROOT_DIR/assets/css" "$PLUGIN_DIR/assets/"

(
  cd "$DIST_DIR"
  zip -q -r "$(basename "$ARCHIVE")" oilpriceapi-widget
)

php "$ROOT_DIR/tests/claims.php"
unzip -tq "$ARCHIVE"

echo "$ARCHIVE"
