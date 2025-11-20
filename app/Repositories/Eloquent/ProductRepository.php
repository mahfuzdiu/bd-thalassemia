<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    private Product $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function paginate(int $perPage = 20)
    {
        return $this->model->with('variants')->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
