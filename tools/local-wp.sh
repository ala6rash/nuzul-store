#!/usr/bin/env bash
# Spin up a throwaway local WordPress + WooCommerce (SQLite, no MySQL needed) with the
# Nuzul theme symlinked in, run the one-click store setup, and serve it on :8080.
#
#   tools/local-wp.sh            # install into .local-wp/ and start the server
#   tools/local-wp.sh serve      # just start the server again
#
# Requires: php 8.1+ (pdo_sqlite, gd, mbstring, zip), git, curl, unzip.
set -euo pipefail
REPO=$(cd "$(dirname "$0")/.." && pwd)
DIR="$REPO/.local-wp"
SITE="$DIR/site"
PORT=${PORT:-8080}
WP="php $DIR/wp-cli.phar --allow-root --path=$SITE"

serve() {
  cat > "$DIR/router.php" <<'EOF'
<?php
$root = __DIR__ . '/site';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path !== '/' && is_file($root . $path)) {
    if (substr($path, -4) === '.php') { chdir(dirname($root . $path)); require $root . $path; return true; }
    return false;
}
chdir($root); $_SERVER['SCRIPT_NAME'] = '/index.php'; require $root . '/index.php';
EOF
  echo "WordPress on http://localhost:$PORT  (admin: admin / admin)"
  PHP_CLI_SERVER_WORKERS=4 php -S "localhost:$PORT" -t "$SITE" "$DIR/router.php"
}

if [[ "${1:-}" == "serve" ]]; then serve; exit; fi

mkdir -p "$DIR" && cd "$DIR"
[[ -d site ]] || git clone -q --depth 1 --branch 6.8.3 https://github.com/WordPress/WordPress.git site
[[ -f wp-cli.phar ]] || curl -sSfL -o wp-cli.phar https://github.com/wp-cli/wp-cli/releases/download/v2.12.0/wp-cli-2.12.0.phar
[[ -d sqlite ]] || git clone -q --depth 1 https://github.com/WordPress/sqlite-database-integration.git sqlite
[[ -f woocommerce.zip ]] || curl -sSfL -o woocommerce.zip https://github.com/woocommerce/woocommerce/releases/download/10.2.2/woocommerce.zip

P="$SITE/wp-content/plugins"
rm -rf "$P/sqlite-database-integration" && cp -rL sqlite/packages/plugin-sqlite-database-integration "$P/sqlite-database-integration"
sed -e "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$P/sqlite-database-integration#" -e "s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#" \
  sqlite/packages/plugin-sqlite-database-integration/db.copy > "$SITE/wp-content/db.php"
[[ -d "$P/woocommerce" ]] || unzip -q woocommerce.zip -d "$P"
ln -sfn "$REPO/theme/nuzul" "$SITE/wp-content/themes/nuzul"

cat > "$SITE/wp-config.php" <<'EOF'
<?php
define('DB_NAME','wp');define('DB_USER','');define('DB_PASSWORD','');define('DB_HOST','');define('DB_CHARSET','utf8mb4');define('DB_COLLATE','');
define('DB_DIR', __DIR__ . '/wp-content/database/'); define('DB_FILE','wp.sqlite');
$table_prefix='wp_';
define('WP_DEBUG',true);define('WP_DEBUG_LOG',true);define('WP_DEBUG_DISPLAY',false);
foreach(['AUTH_KEY','SECURE_AUTH_KEY','LOGGED_IN_KEY','NONCE_KEY','AUTH_SALT','SECURE_AUTH_SALT','LOGGED_IN_SALT','NONCE_SALT'] as $k) define($k,'local-dev-only');
if(!defined('ABSPATH')) define('ABSPATH', __DIR__.'/');
require_once ABSPATH.'wp-settings.php';
EOF

# Arabic language packs come from wordpress.org; if that is unreachable, force RTL so the layout is testable.
mkdir -p "$SITE/wp-content/mu-plugins"
cat > "$SITE/wp-content/mu-plugins/local-dev.php" <<'EOF'
<?php
// Local development only.
add_action('init', function(){ global $wp_locale; $wp_locale->text_direction = 'rtl'; }, 0);
add_filter('language_attributes', fn() => 'dir="rtl" lang="ar"');
add_filter('loop_shop_per_page', fn() => 24, 99); // whole catalogue on one shop page (for the static export)
EOF

if ! $WP core is-installed 2>/dev/null; then
  $WP core install --url="http://localhost:$PORT" --title="نُزُل" --admin_user=admin --admin_password=admin --admin_email=dev@example.com --skip-email
  $WP language core install ar --activate 2>/dev/null || echo "(Arabic language pack not reachable — RTL forced by mu-plugin)"
  $WP plugin activate woocommerce
  $WP theme activate nuzul
  $WP eval 'print_r(nuzul_run_setup(["whatsapp"=>"","instagram"=>"nuzul.jo","email"=>"hello@example.com","products"=>true]));'
  $WP option update blogdescription "رفاهية الفندق، في منزلك"
  $WP rewrite flush
fi
serve
