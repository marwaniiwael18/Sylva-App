#!/bin/bash
set -e

echo "🚀 Starting Sylva App deployment..."

# Wait for database to be ready
echo "⏳ Waiting for database..."
sleep 10

# Run migrations
echo "📦 Running database migrations..."
php artisan migrate --force

# Clear all Laravel caches
echo "🧹 Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
# Don't cache routes for now to avoid conflicts
# php artisan route:cache

# Create storage link if needed
echo "🔗 Creating storage link..."
php artisan storage:link || true

# Generate optimized autoloader (without route caching for now)
echo "⚡ Optimizing autoloader..."
composer dump-autoload --optimize

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "✅ Deployment setup complete!"