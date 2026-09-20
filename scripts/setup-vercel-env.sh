#!/usr/bin/env bash
# One-time Vercel setup: reads values from local .env and pushes them to the
# linked Vercel project as production environment variables.
#
# Usage:
#   vercel login        (once, opens the browser)
#   vercel link         (once, inside this project)
#   bash scripts/setup-vercel-env.sh
#   vercel --prod
#
# After the first deploy, set APP_URL in the Vercel dashboard to the new
# https://<project>.vercel.app domain (the script deliberately skips it).

set -euo pipefail
cd "$(dirname "$0")/.."

if [ ! -f .env ]; then
    echo "No .env file found." >&2
    exit 1
fi

set -a
source .env
set +a

# Values that must differ from local settings on Vercel.
export APP_ENV=production
export APP_DEBUG=false
export CACHE_STORE=database
export QUEUE_CONNECTION=sync
export LOG_CHANNEL=stderr

vars=(
    APP_NAME APP_ENV APP_DEBUG APP_TIMEZONE APP_KEY
    DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD DB_SSLMODE
    SESSION_DRIVER SESSION_LIFETIME CACHE_STORE QUEUE_CONNECTION
    LOG_CHANNEL MAIL_MAILER MAIL_FROM_ADDRESS MAIL_FROM_NAME GROQ_API_KEY
)

for key in "${vars[@]}"; do
    value="${!key:-}"
    if [ -z "$value" ]; then
        echo "Skipping $key (not set in .env)" >&2
        continue
    fi
    printf '%s' "$value" | vercel env add "$key" production
    echo "Set $key"
done
