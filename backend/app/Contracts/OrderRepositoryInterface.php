<?php

namespace App\Contracts;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function createOrder(array $data): Order;
}
