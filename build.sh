#!/bin/bash
# Railway / Render / Fly.io build script

set -e

echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing Node dependencies..."
npm ci

echo "Building frontend assets..."
npm run build

echo "Build complete."
