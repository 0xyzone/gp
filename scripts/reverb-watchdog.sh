#!/bin/bash
# ==============================================================================
# Laravel Reverb Watchdog for cPanel
# Automatically starts and keeps Laravel Reverb running 24/7.
# ==============================================================================

# Determine base home directory and project path
CURRENT_USER=$(whoami)
PROJECT_DIR="${HOME}/gp"

# Fallback in case ${HOME} is not set in a cron subshell
if [ ! -d "$PROJECT_DIR" ]; then
    PROJECT_DIR="/home/${CURRENT_USER}/gp"
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

# Check if Reverb process is already active
if pgrep -f "artisan reverb:start" > /dev/null 2>&1; then
    # Already running smoothly
    exit 0
fi

# If not running, change to project directory and start Reverb daemon
cd "$PROJECT_DIR" || exit 1

mkdir -p storage/logs
nohup "$PHP_BIN" artisan reverb:start --host=0.0.0.0 --port=8080 > storage/logs/reverb.log 2>&1 &

echo "Laravel Reverb started using $PHP_BIN on port 8080."
