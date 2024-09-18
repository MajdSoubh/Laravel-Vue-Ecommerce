<?php

namespace App\Rules;

use App\Events\Cart\OperationFailed;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductQuantityChecker implements ValidationRule
{


    // The Product's id get updated.
    private $productId;

    // The Product's requested quantity.
    private $requestedQuantity;

    function __construct($productId, $quantity)
    {
        $this->productId = $productId;
        $this->requestedQuantity = $quantity;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $product = Product::find($this->productId);
        if (!$product)
        {
            OperationFailed::dispatch('The requested product is not available');
            $fail('The requested product is not available');
        }
        else  if ($product->quantity < $this->requestedQuantity)
        {
            OperationFailed::dispatch('The requested quantity is not available');
            $fail('The requested quantity is not available');
        }
    }
}
