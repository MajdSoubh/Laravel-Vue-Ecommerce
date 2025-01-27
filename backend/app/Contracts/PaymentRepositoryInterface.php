<?php

namespace App\Contracts;

use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function create(array $data): Payment;
}
