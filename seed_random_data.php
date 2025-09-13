<?php

/**
 * Standalone script to generate random data for the box_web system
 * Run this script from the box_web directory: php seed_random_data.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Seeder;
use Database\Seeders\RandomDataSeeder;

// Load environment variables
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Database configuration
$config = [
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'database' => $_ENV['DB_DATABASE'] ?? 'we-courier',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
];

// Initialize Capsule
$capsule = new Capsule;
$capsule->addConnection($config);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "Starting random data generation for box_web system...\n";
echo "================================================\n\n";

try {
    // Run the random data seeder
    $seeder = new RandomDataSeeder();
    $seeder->run();
    
    echo "\n================================================\n";
    echo "Random data generation completed successfully!\n";
    echo "Generated data includes:\n";
    echo "- 15 Merchants with shops and delivery charges\n";
    echo "- 20 Delivery men\n";
    echo "- 100 Parcels with various statuses and events\n";
    echo "- Financial data (accounts, payments, statements)\n";
    echo "- Income and expense records\n";
    echo "\nYou can now test the system with this random data.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
