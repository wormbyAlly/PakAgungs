<?php

namespace App\Services\Accounting\DTO;

class SaleSummary
{
    public function __construct(
        public float $subtotal,
        public float $discount,
        public float $grandTotal
    ) {}

    public function hasDiscount(): bool
    {
        return $this->discount > 0;
    }
}
