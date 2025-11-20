<?php

namespace App\Repositories\Contracts;

interface ProductVariantsRepositoryInterface
{
    public function insert(array $data);
    public function updateAndInsert(array $updatedVariants);
}
