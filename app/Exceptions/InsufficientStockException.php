<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(public readonly string $productName)
    {
        parent::__construct("Stok produk \"{$productName}\" tidak mencukupi.");
    }
}
