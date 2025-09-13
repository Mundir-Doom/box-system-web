#!/bin/bash

# Script to generate random test data for the box_web system
# This script will create comprehensive test data to verify system functionality

echo "=========================================="
echo "Box Web System - Random Data Generator"
echo "=========================================="
echo ""

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    echo "Error: Please run this script from the box_web directory"
    exit 1
fi

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "Error: .env file not found. Please configure your database settings first."
    exit 1
fi

echo "Generating random test data..."
echo "This will create:"
echo "- 15 Merchants with shops and delivery charges"
echo "- 20 Delivery men"
echo "- 100 Parcels with various statuses and events"
echo "- Financial data (accounts, payments, statements)"
echo "- Income and expense records"
echo ""

# Ask for confirmation
read -p "Do you want to continue? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Operation cancelled."
    exit 0
fi

echo ""
echo "Starting data generation..."

# Run the Laravel command
php artisan seed:random-data

if [ $? -eq 0 ]; then
    echo ""
    echo "=========================================="
    echo "✅ Random data generation completed successfully!"
    echo "=========================================="
    echo ""
    echo "You can now:"
    echo "1. Login to the admin panel to view the generated data"
    echo "2. Test parcel creation, tracking, and delivery workflows"
    echo "3. Verify financial reports and statements"
    echo "4. Test merchant and delivery man management"
    echo ""
    echo "Default login credentials (if not changed):"
    echo "Admin: admin@wemaxdevs.com / 12345678"
    echo "Merchant: merchant@wemaxdevs.com / 12345678"
    echo "Delivery Man: deliveryman@wemaxit.com / 12345678"
else
    echo ""
    echo "=========================================="
    echo "❌ Error occurred during data generation"
    echo "=========================================="
    echo "Please check the error messages above and try again."
    exit 1
fi
