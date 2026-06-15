#!/bin/bash

# Optimize configuration and routing for production speed
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "Running migrations..."
php artisan migrate --force

# Automatically seed the database with mock data if it is empty
echo "Checking if database needs seeding..."
php artisan tinker --execute="if (\App\Models\User::count() === 0) { echo 'Seeding database...'; \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]); } else { echo 'Database already seeded.'; }"
