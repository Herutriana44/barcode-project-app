# Implementation Plan: Pivot Tables, Search Feature & Shortened Barcode Design

## Context
The user requested:
1. Create pivot table relationships in `items-barcodes` and `company barcodes` tables
2. Add a search feature in `CompanyBarcode` section
3. Shorten barcode design length

## Goals
- Build a relational pivot table to track item-company-barcode relationships
- Implement a searchable interface for company barcodes with filters
- Optimize barcode generation to produce shorter identifiers while maintaining uniqueness

## Tasks

### 1. Database Schema: Pivot Table
**File:** `database/migrations/2024_01_01_000000_create_item_company_barcode_pivot_table.php`
- Create migration for new pivot table `item_company_barcode_pivot`
- Columns:
  - `item_id` (FK → items.id)
  - `company_id` (FK → companies.id)
  - `barcode_id` (FK → item_barcodes.id)
  - `quantity` (default 1)
  - `posisi_rak` (nullable string)
  - `tingkat` (nullable string)
  - timestamps
- Add indexes on `company_id`, `posisi_rak`, and `tingkat`

### 2. Model Updates
**File:** `app/Models/Company.php` & `app/Models/Item.php`
- Add `hasMany` relationships in `Company` for `companyBarcodes` and `companyItems`
- Add `hasManyThrough` or custom relationships in `Item` for pivot access
- Ensure backward compatibility with existing methods

### 3. Controller Enhancements
**File:** `app/Http/Controllers/CompanyBarcodeController.php`
- Add search endpoints supporting:
  - Barcode ID filtering (`?search=CB-`)
  - Part name search (`part_name`)
  - Location filter (`posisi_rak`)
  - Pagination (15 items per page)
- Implement index pagination with search parameters
- Maintain existing create/update/delete flows with minimal disruption

### 4. Barcode Design Optimization
**File:** `app/Support/BarcodeQrCodes.php`
- Refactor `code128SvgForScan` and `qrSvgForScan` to:
  - Use compact barcode IDs (e.g., `CB-123-456` instead of long UUIDs)
  - Reduce image dimensions by 20% while preserving scan reliability
  - Simplify text rendering to minimize width/height expansion
- Add unit tests in `tests/Feature/BarcodeDesignTest.php` to verify size constraints

### 5. Testing & Verification
**File:** `tests/Feature/CompanyBarcodeSearchTest.php`
- Cover all search filters and pagination cases
****File:** `tests/Feature/BarcodeDesignTest.php`
- Verify shortened identifiers are unique and scannable
- Confirm image dimensions meet minimum QR standards (≥21x21px)

## Verification Steps
1. Run migrations: `php artisan migrate`
2. Test search functionality in `/company-barcodes`
3. Confirm new pivot relationships are persisted correctly
4. Verify barcode images are shorter but still scannable
5. Ensure existing functionality remains intact

## Dependencies
- None new; uses existing Eloquent relationships and Laravel routing
- Requires PHP 8.1+ and Laravel framework as currently configured

## Timeline
- Complete implementation within 2 development cycles (≈2 days)