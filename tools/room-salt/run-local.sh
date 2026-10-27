#!/usr/bin/env bash
# Run the Room & Salt store locally with WordPress Playground (needs Node 20.18+).
# Opens http://127.0.0.1:9400 with WooCommerce, Arabic, and all products set up.
set -e
ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
MSYS_NO_PATHCONV=1 npx -y @wp-playground/cli@latest server --port=9400 \
  --blueprint="$ROOT/tools/room-salt/playground-blueprint.json" \
  --mount-dir-before-install "$ROOT/theme/room-salt" /wordpress/wp-content/themes/room-salt
