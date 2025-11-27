<?php

namespace App\Modules\Product\DTOs;

class UpdateProductDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $price,
        public string $description,
    ) {}
}
