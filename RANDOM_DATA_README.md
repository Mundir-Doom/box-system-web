# Random Data Generator for Box Web System

This document explains how to generate random test data for the box_web courier management system to verify that everything is working properly.

## Overview

The random data generator creates comprehensive test data including:
- **15 Merchants** with shops and delivery charges
- **20 Delivery Men** with different statuses
- **100 Parcels** with various statuses and tracking events
- **Financial Data** including accounts, payments, and statements
- **Income and Expense** records

## Quick Start

### Option 1: Using the Bash Script (Recommended)
```bash
cd /path/to/box_web
./generate_test_data.sh
```

### Option 2: Using Laravel Artisan Command
```bash
cd /path/to/box_web
php artisan seed:random-data
```

### Option 3: Using the Standalone PHP Script
```bash
cd /path/to/box_web
php seed_random_data.php
```

## Prerequisites

1. **Database Setup**: Ensure your database is configured in the `.env` file
2. **Dependencies**: Make sure all Laravel dependencies are installed (`composer install`)
3. **Migrations**: Run database migrations first (`php artisan migrate`)

## Generated Data Details

### Merchants
- 15 merchants with unique business names, emails, and phone numbers
- Each merchant has 1-3 shops
- Custom delivery charges for each merchant
- Random current and opening balances

### Delivery Men
- 20 delivery men with different statuses (active/inactive)
- Random delivery, pickup, and return charges
- Various current balances and salary amounts

### Parcels
- 100 parcels with realistic data
- Various statuses: pending, picked up, in transit, delivered, cancelled, etc.
- Complete tracking events for each parcel
- Random weights, prices, and delivery charges

### Financial Data
- 10 bank accounts with random balances
- 50 merchant payments
- 100 merchant statements
- 100 deliveryman statements
- 30 income records
- 30 expense records

## Default Login Credentials

After running the seeder, you can use these default credentials:

- **Admin**: admin@wemaxdevs.com / 12345678
- **Merchant**: merchant@wemaxdevs.com / 12345678
- **Delivery Man**: deliveryman@wemaxit.com / 12345678

## Testing the System

Once the random data is generated, you can test:

1. **Parcel Management**
   - Create new parcels
   - Track parcel status
   - Update delivery status
   - Generate invoices

2. **Merchant Operations**
   - View merchant dashboards
   - Check delivery charges
   - Review payment history
   - Generate reports

3. **Delivery Man Operations**
   - Assign parcels to delivery men
   - Track delivery progress
   - Manage payments and commissions

4. **Financial Reports**
   - View income statements
   - Check expense reports
   - Generate financial summaries
   - Review account balances

## Files Created

- `database/seeders/RandomDataSeeder.php` - Main seeder class
- `app/Console/Commands/SeedRandomData.php` - Artisan command
- `seed_random_data.php` - Standalone PHP script
- `generate_test_data.sh` - Bash script for easy execution

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check your `.env` file database configuration
   - Ensure the database server is running
   - Verify database credentials

2. **Missing Dependencies**
   - Run `composer install` to install PHP dependencies
   - Ensure Faker library is available

3. **Permission Issues**
   - Make sure the bash script is executable: `chmod +x generate_test_data.sh`
   - Check file permissions in the storage directory

4. **Memory Issues**
   - If you encounter memory errors, reduce the number of records in the seeder
   - Increase PHP memory limit in php.ini

### Resetting Data

To clear existing data and start fresh:
```bash
php artisan migrate:fresh --seed
```

Or use the fresh option with the command:
```bash
php artisan seed:random-data --fresh
```

## Customization

You can modify the `RandomDataSeeder.php` file to:
- Change the number of records generated
- Adjust the data ranges (prices, weights, etc.)
- Add more realistic data patterns
- Include additional fields or relationships

## Support

If you encounter any issues with the random data generator, check:
1. Laravel logs in `storage/logs/`
2. Database connection settings
3. PHP error logs
4. Ensure all required models and enums exist

The generated data should provide a comprehensive test environment for verifying all system functionality.
