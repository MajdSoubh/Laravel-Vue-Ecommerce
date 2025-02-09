<?php

namespace App\Rules;

use App\Events\Notification;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BulkProductExistenceChecker implements ValidationRule
{

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $items, Closure $fail): void
    {
        $productIds = array_column($items, 'product_id');

        $uniqueProductIds = array_unique($productIds);

        $existingProductCount = Product::whereIn('id', $uniqueProductIds)->count();

        if ($existingProductCount !== count($productIds))
        {
            $fail(__('validation.invalid_product'));
            Notification::dispatch(__('validation.invalid_product'), 'error');
        }
    }
}
