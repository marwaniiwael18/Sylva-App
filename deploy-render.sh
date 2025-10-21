#!/bin/bash
set -e

echo "🚀 Triggering Render deployment..."

# Trigger Render deployment
curl -X POST "$RENDER_DEPLOY_HOOK_URL" \
  -H "Content-Type: application/json" \
  --silent --show-error --max-time 30

echo "✅ Render deployment triggered successfully!"