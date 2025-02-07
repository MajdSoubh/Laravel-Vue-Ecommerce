<?php

namespace App\Rules;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ParentCategoryChecker implements ValidationRule
{

    // The category's id get updated.
    private $categoryId;

    function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the parentId equal the category Id.
        if ($value == $this->categoryId)
        {
            $fail('You cannot choose current category as a parent category.');
        }

        $subCategoriesIds = Category::find($this->categoryId)->subCategories->pluck('id')->toArray();

        if (in_array($value, $subCategoriesIds))
        {
            $fail('You cannot choose category as a parent which is already a child of the category.');
        }
    }
}
