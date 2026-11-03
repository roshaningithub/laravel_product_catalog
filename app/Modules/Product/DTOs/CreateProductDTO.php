<?php

namespace App\Modules\Product\DTOs;

class CreateProductDTO
{
    public function __construct(
        public string $name,
        public string $price,
        public ?string $description = null,
    ) {}
}
