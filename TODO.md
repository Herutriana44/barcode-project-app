# TODO - Barcode Web App

## Phase 1: Documentation and Design
- [x] Create ERD.md
- [x] Create TODO.md

## Phase 2: Package Installation
- [x] Install laravel/breeze (Blade stack)
- [x] Install picqer/php-barcode-generator or milon/barcode
- [x] Install html5-qrcode or @zxing/library (npm)
- [x] Install tailwindcss (via Breeze)
- [x] Install @tailwindcss/forms

## Phase 3: Shell Script
- [x] Create first.sh with artisan make commands
- [x] Make first.sh executable (chmod +x)

## Phase 4: Database Migrations
- [x] Create create_companies_table migration
- [x] Create create_items_table migration
- [x] Create create_item_receivings_table migration
- [x] Create create_item_barcodes_table migration
- [x] Create create_company_items_table migration
- [x] Create create_company_barcodes_table migration
- [ ] Run migrations (requires MySQL/SQLite - run: php artisan migrate)

## Phase 5: Models
- [x] Create Company model with relationships
- [x] Create Item model with relationships
- [x] Create ItemReceiving model with relationships
- [x] Create ItemBarcode model with relationships
- [x] Create CompanyItem model with relationships
- [x] Create CompanyBarcode model with relationships

## Phase 6: Seeders
- [x] Create CompanySeeder
- [x] Create ItemSeeder
- [x] Create ItemReceivingSeeder
- [x] Update DatabaseSeeder to call all seeders
- [ ] Run seeders (after migrate: php artisan db:seed)

## Phase 7: Controllers
- [x] Create ItemBarcodeController
- [x] Create CompanyBarcodeController
- [x] Create ScanController

## Phase 8: Routes
- [x] Configure web.php with auth middleware
- [x] Add dashboard route
- [x] Add item-barcodes routes
- [x] Add company-barcodes routes
- [x] Add scan routes

## Phase 9: Views
- [x] Create layouts/app.blade.php (Breeze)
- [x] Create dashboard.blade.php
- [x] Create item-barcodes/create.blade.php
- [x] Create item-barcodes/show.blade.php
- [x] Create item-barcodes/index.blade.php
- [x] Create company-barcodes/create.blade.php
- [x] Create company-barcodes/show.blade.php
- [x] Create company-barcodes/index.blade.php
- [x] Create scan/index.blade.php
- [x] Add scan.js for camera scanning

## Phase 10: Barcode Generation
- [x] Implement barcode image generation (Item)
- [x] Implement barcode image generation (Company)
- [x] Implement scan lookup API

## Phase 11: Testing
- [ ] Test Barcode Barang flow
- [ ] Test Barcode Perusahaan flow
- [ ] Test scan functionality
