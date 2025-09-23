#!/bin/bash

# Sylva Project Setup Script
# This script will set up the Sylva project for development

echo "🌱 Welcome to Sylva Setup!"
echo "================================"

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.2 or higher."
    exit 1
fi

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer."
    exit 1
fi

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 18 or higher."
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ NPM is not installed. Please install NPM."
    exit 1
fi

echo "✅ All prerequisites are installed!"
echo ""

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "⚙️  Creating environment file..."
    cp .env.example .env
    php artisan key:generate
else
    echo "⚙️  Environment file already exists"
fi

# Run database migrations
echo "🗄️  Setting up database..."
php artisan migrate

# Build frontend assets
echo "🏗️  Building frontend assets..."
npm run build

# Clear all caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "🎉 Setup complete!"
echo ""
echo "To start the development server:"
echo "  php artisan serve"
echo ""
echo "Demo credentials:"
echo "  Email: demo@sylva.com"
echo "  Password: demo123"
echo ""
echo "🌱 Happy coding with Sylva!"