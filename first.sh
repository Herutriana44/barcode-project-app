#!/bin/bash
# Barcode Web App - Generate migrations, models, seeders, and controllers
# Run from project root: ./first.sh

set -e

echo "=== Creating Migrations ==="
php artisan make:migration create_companies_table
php artisan make:migration create_items_table
php artisan make:migration create_item_receivings_table
php artisan make:migration create_item_barcodes_table
php artisan make:migration create_company_items_table
php artisan make:migration create_company_barcodes_table

echo "=== Creating Models ==="
php artisan make:model Company
php artisan make:model Item
php artisan make:model ItemReceiving
php artisan make:model ItemBarcode
php artisan make:model CompanyItem
php artisan make:model CompanyBarcode

echo "=== Creating Seeders ==="
php artisan make:seeder CompanySeeder
php artisan make:seeder ItemSeeder
php artisan make:seeder ItemReceivingSeeder

echo "=== Creating Controllers ==="
php artisan make:controller ItemBarcodeController
php artisan make:controller CompanyBarcodeController
php artisan make:controller ScanController

echo "=== Done! ==="
echo "Next steps:"
echo "1. Edit migration files with table structure"
echo "2. Edit models with relationships and fillable"
echo "3. Edit seeders with sample data"
echo "4. Edit controllers with logic"
echo "5. Run: php artisan migrate"
echo "6. Run: php artisan db:seed"
