<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the active categories.
     */
    public function index()
    {

        $categories = Category::active()->get();

        return CategoryResource::collection($categories);
    }
}
