<?php

namespace App\Http\Controllers\Admin;

use App\Enums\HttpStatusCode;
use App\Http\Requests\Admin\Category\StoreRequest as CategoryStoreRequest;
use App\Http\Requests\Admin\Category\UpdateRequest;
use App\Http\Resources\Category\CategoryTreeResource;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'asc');
        $perPage = request('per_page', '10');
        $categories = Category::where('name', 'like', "%{$search}%")->with('mainCategory')->orderBy($sortField, $sortDirection)->paginate($perPage);


        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        // Add created by, updated by columns
        $policy = ['created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id];

        $category = Category::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent'),
            'active' => $request->input('active'),
        ] + $policy);

        return (new CategoryResource($category))->additional(['message' => 'Category has been created successfully'])->response()->setStatusCode(HttpStatusCode::CREATED->value);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::with('mainCategory')->find($id);

        if (is_null($category))
        {
            return response()->json(['message' => 'No category exists with the provided id'], HttpStatusCode::NOT_FOUND->value);
        }

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $category = Category::find($id);

        if (is_null($category))
        {
            return response()->json(['message' => 'No category exists with the provided id'], HttpStatusCode::NOT_FOUND->value);
        }

        // Add updated by columns
        $policy = ['updated_by' => auth()->user()->id];

        $category->update([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent'),
            'active' => $request->input('active'),
        ] + $policy);

        return (new CategoryResource($category))->additional(['message' => 'Category has been updated successfully'])->response()->setStatusCode(HttpStatusCode::OK->value);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (is_null($category))
        {
            return response()->json(['message' => 'No category exists with the provided id'], HttpStatusCode::NOT_FOUND->value);
        }

        $category->delete();

        return (new CategoryResource($category))->additional(['message' => 'Category has been deleted successfully'])->response()->setStatusCode(HttpStatusCode::OK->value);
    }

    public function getAsTree()
    {
        return  CategoryTreeResource::collection(Category::getAsTree(1));
    }
}
