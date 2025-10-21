#!/bin/bash
set -e

echo "🚀 Starting Sylva App deployment..."

# Wait for database to be ready
echo "⏳ Waiting for database..."
sleep 10

# Run migrations
echo "📦 Running database migrations..."
php artisan migrate --force

# Clear and cache config
echo "⚙️ Clearing and caching config..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan view:clear

# Create storage link if needed
echo "🔗 Creating storage link..."
php artisan storage:link || true

echo "✅ Deployment setup complete!"