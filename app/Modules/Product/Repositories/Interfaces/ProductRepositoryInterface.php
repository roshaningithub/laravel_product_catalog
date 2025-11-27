<?php

namespace App\Modules\Product\Repositories\Interfaces;

use App\Modules\Product\DTOs\CreateProductDTO;
use App\Modules\Product\DTOs\DeleteProductDTO;
use App\Modules\Product\DTOs\UpdateProductDTO;
use App\Modules\Product\Models\Product;

interface ProductRepositoryInterface
{
    public function create(CreateProductDTO $data): Product;
    public function update(UpdateProductDTO $data): Product;
    public function delete(DeleteProductDTO $data): bool;
}
