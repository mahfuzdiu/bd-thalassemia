<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface
{
    public function paginate(int $perPage = 20);
    public function create(array $data);
}
