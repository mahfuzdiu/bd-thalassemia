<?php

namespace App\Console\Commands;

use App\Imports\ProductsVariantsImport;
use App\Services\ProductImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportProductsCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products and variants from CSV';

    /**
     * Execute the console command.
     */
    public function handle(ProductImportService $pcs)
    {
        $this->info("Processing CSV");
        try {
            $pcs = app(ProductImportService::class);
            $filePath = storage_path('app/products.csv');
            Excel::import(new ProductsVariantsImport($pcs), $filePath);
            $this->info('CSV processed successfully.');
        } catch (\Exception $e) {
            $this->error('Error processing CSV: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
