<?php

namespace App\Console\Commands;

use App\Support\InventorySpreadsheet;
use Illuminate\Console\Command;

class ExportInventoryExcelTemplates extends Command
{
    protected $signature = 'inventory:export-templates';

    protected $description = 'Menulis template import Excel ke storage/app/templates/';

    public function handle(): int
    {
        $dir = storage_path('app/templates');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        InventorySpreadsheet::writeXlsxToPath($dir.'/template-import-barang-fg.xlsx', [
            InventorySpreadsheet::fgHeaderRow(),
            InventorySpreadsheet::fgExampleRow(),
        ]);

        InventorySpreadsheet::writeXlsxToPath($dir.'/template-import-perusahaan.xlsx', [
            InventorySpreadsheet::companyHeaderRow(),
            InventorySpreadsheet::companyExampleRow(),
        ]);

        $this->info('Berhasil: storage/app/templates/template-import-barang-fg.xlsx');
        $this->info('Berhasil: storage/app/templates/template-import-perusahaan.xlsx');

        return self::SUCCESS;
    }
}
