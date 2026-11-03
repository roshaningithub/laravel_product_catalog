<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\DTOs\CreateProductDTO;
use App\Modules\Product\DTOs\DeleteProductDTO;
use App\Modules\Product\DTOs\UpdateProductDTO;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Repositories\Interfaces\ProductRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(protected ProductRepositoryInterface $productRepository) {}

    public function create(CreateProductDTO $data): Product
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepository->create($data);

            DB::commit();

            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(UpdateProductDTO $data): Product
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepository->update($data);

            DB::commit();

            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(DeleteProductDTO $data): bool
    {
        DB::beginTransaction();
        try {
            $deleted = $this->productRepository->delete($data);

            DB::commit();

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
