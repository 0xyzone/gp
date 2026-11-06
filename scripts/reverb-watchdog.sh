#!/bin/bash
# ==============================================================================
# Laravel Reverb Watchdog for cPanel
# Automatically starts and keeps Laravel Reverb running 24/7.
# ==============================================================================

# Determine base home directory and project path
CURRENT_USER="${USER:-suminshr}"
PROJECT_DIR="/home/suminshr/gp"

# Fallback if running under different context or HOME variable is available
if [ ! -d "$PROJECT_DIR" ] && [ -n "$HOME" ]; then
    PROJECT_DIR="${HOME}/gp"
fi

# Ensure project directory exists
if [ ! -d "$PROJECT_DIR" ]; then
    echo "Error: Laravel project directory '$PROJECT_DIR' does not exist." >&2
    exit 1
fi

# Automatically detect the best PHP 8.4+ binary available on cPanel
PHP_BIN=""
CANDIDATES=(
    "/usr/local/bin/ea-php84"
    "/opt/cpanel/ea-php84/root/usr/bin/php"
    "/usr/local/bin/ea-php83"
    "/opt/cpanel/ea-php83/root/usr/bin/php"
    "$(which php 2>/dev/null)"
    "/usr/local/bin/php"
    "/usr/bin/php"
)

for bin in "${CANDIDATES[@]}"; do
    if [ -n "$bin" ] && [ -x "$bin" ]; then
        PHP_BIN="$bin"
        break
    fi
done

if [ -z "$PHP_BIN" ]; then
    echo "Error: No executable PHP binary could be found." >&2
    exit 1
fi

# Check if this project's Reverb process is already active
if pgrep -f "$PROJECT_DIR/artisan reverb:start" > /dev/null 2>&1; then
    # Already running smoothly for this project
    exit 0
fi

# If not running, change to project directory and start Reverb daemon
cd "$PROJECT_DIR" || exit 1

mkdir -p storage/logs
nohup "$PHP_BIN" artisan reverb:start --host=0.0.0.0 --port=8080 > storage/logs/reverb.log 2>&1 &

echo "Laravel Reverb started using $PHP_BIN on port 8080."

