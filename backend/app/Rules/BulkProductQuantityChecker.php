<?php

namespace App\Rules;

use App\Events\Notification;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BulkProductQuantityChecker implements ValidationRule
{

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $items, Closure $fail): void
    {
        $productIds = array_column($items, 'product_id');

        $aggregatedQuantities = [];

        // Aggregate quantities by product_id (suppose items has duplicated product_ids)
        foreach ($items as $item)
        {
            $productId = $item['product_id'];
            $aggregatedQuantities[$productId] = ($aggregatedQuantities[$productId] ?? 0) + $item['quantity'];
        }

        $existingProducts = Product::select('id', 'quantity')->whereIn('id', $productIds)->pluck('quantity', 'id');


        foreach ($aggregatedQuantities as $productId => $totalRequestedQuantity)
        {
            if ($existingProducts[$productId] < $totalRequestedQuantity)
            {
                $fail(__('validation.quantity_not_available'));
                Notification::dispatch(__('validation.quantity_not_available'), 'error');
                break;
            }
        }
    }
}
