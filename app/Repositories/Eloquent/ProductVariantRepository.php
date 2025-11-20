<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductVariantsRepositoryInterface;

class ProductVariantRepository implements ProductVariantsRepositoryInterface
{
    private ProductVariant $model;

    public function __construct(ProductVariant $model)
    {
        $this->model = $model;
    }

    public function insert(array $data)
    {
        $this->model->insert($data);
    }

    public function updateAndInsert($updatedVariants)
    {
        $this->model->upsert($updatedVariants, ['variant_sku'], ['price', 'stock', 'updated_at']);
    }
}
