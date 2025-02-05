<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of all active categories.
     *
     * This method retrieves all categories marked as "active" and returns them as a collection
     * of `CategoryResource` objects.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        $categories = Category::active()->get();
        return CategoryResource::collection($categories);
    }
}
