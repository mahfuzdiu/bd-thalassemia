<?php

namespace App\Imports;

use App\Services\ProductImportService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsVariantsImport implements WithHeadingRow, WithChunkReading, ToCollection
{
    public ProductImportService $pis;

    public function __construct(ProductImportService $pis){
        $this->pis = $pis;
    }

    public function chunkSize(): int
    {
        return 5; // number of rows to load at once
    }

    public function rules(): array
    {
        return [

            // ---- Product Level Fields ----
            '*.product_sku'  => ['required', 'string', 'max:255'],
            '*.name'         => ['required', 'string', 'max:255'],
            '*.description'  => ['nullable', 'string'],

            // ---- Variant Level Fields ----
            '*.variant_sku'  => ['required', 'string', 'max:255'],
            '*.price'        => ['required', 'numeric', 'min:0'],
            '*.stock'        => ['required', 'integer', 'min:0'],

            // ---- Dynamic Attribute Columns ----
            '*.attr_*'       => ['nullable', 'string', 'max:255'],
        ];
    }

    public function collection(Collection $rows)
    {
        $grouped = [];

        foreach ($rows as $row) {
            // Skip empty rows
            if (empty(array_filter($row->toArray()))) continue;

            $productSku = trim($row['product_sku']);

            // Initialize product if not exists
            if (!isset($grouped[trim($productSku)])) {
                $grouped[trim($productSku)] = [
                    'name' => strtolower(trim($row['name'])),
                    'product_sku' => strtoupper(trim($row['product_sku'])),
                    'description' => trim($row['description']),
                    'variants' => []
                ];
            }

            // Collect dynamic attributes
            $attributes = [];
            foreach ($row->toArray() as $key => $value) {
                if (!is_null(trim($value)) && !empty(trim($value)) && str_starts_with(trim($key), 'attr_')) {
                    $attrName = substr(trim($key), 5); // remove "attr_"
                    $attributes[$attrName] = trim($value);
                }
            }

            // Add variant
            $grouped[trim($productSku)]['variants'][] = [
                'variant_sku' => strtoupper(trim($row['variant_sku'])),
                'price' => (float) trim($row['price']),
                'stock' => (int) trim($row['stock']),
                'attributes' => $attributes
            ];
        }

        $products = array_values($grouped);

        DB::transaction(function () use ($products){
            $this->pis->insertProducts($products);
            $this->pis->insertVariants($products);
            $this->pis->insertAttributes($products);
            $this->pis->insertAttributeValues($products);
            $this->pis->insertVariantAttributeValuesPivot($products);
        });

        //insert the batch into elastic search
    }
}
