<?php

namespace App\Modules\Product\DTOs;

class DeleteProductDTO
{
    public function __construct(
        public int $id,
    ) {}
}
