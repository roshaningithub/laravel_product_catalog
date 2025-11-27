<?php

namespace App\Modules\Product\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Modules\Product\Services\ProductService;
use App\Modules\Product\DTOs\CreateProductDTO;
use App\Modules\Product\DTOs\UpdateProductDTO;
use App\Modules\Product\DTOs\DeleteProductDTO;
use App\Modules\Product\Requests\CreateProductRequest;
use App\Modules\Product\Requests\UpdateProductRequest;
use App\Modules\Product\Requests\DeleteProductRequest;
use App\Modules\Product\Resources\ProductResource;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function create(CreateProductRequest $request): JsonResponse
    {
        $data = new CreateProductDTO(
            name: $request->name,
            description: $request->description,
            price: $request->price,
        );

        $product = $this->productService->create($data);

        return apiResponse(
            status: true,
            code: 201,
            message: 'Product created successfully.',
            data: new ProductResource($product)
        );
    }

    public function update(UpdateProductRequest $request): JsonResponse
    {
        $data = new UpdateProductDTO(
            id: $request->id,
            name: $request->name,
            description: $request->description,
            price: $request->price,
        );

        $product = $this->productService->update($data);

        return apiResponse(
            status: true,
            code: 200,
            message: 'Product updated successfully.',
            data: new ProductResource($product)
        );
    }

    public function delete(DeleteProductRequest $request): JsonResponse
    {
        $data = new DeleteProductDTO(id: $request->id);

        $this->productService->delete($data);

        return apiResponse(
            status: true,
            code: 200,
            message: 'Product deleted successfully.'
        );
    }
}
