<?php

namespace App\Modules\Product\Repositories\Eloquent;

use App\Modules\Product\DTOs\CreateProductDTO;
use App\Modules\Product\DTOs\DeleteProductDTO;
use App\Modules\Product\DTOs\UpdateProductDTO;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function create(CreateProductDTO $data): Product
    {
        $product = Product::create([
            'name' => $data->name,
            'description' => $data->description,
            'price' => $data->price,
        ]);

        return $product;
    }

    public function update(UpdateProductDTO $data): Product
    {
        $product = Product::findOrFail($data->id);

        $product->update([
            'name' => $data->name,
            'description' => $data->description,
            'price' => $data->price,
        ]);

        return $product;
    }

    public function delete(DeleteProductDTO $data): bool
    {
        $product = Product::findOrFail($data->id);

        return (bool) $product->delete();
    }
}
