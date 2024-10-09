<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Category\StoreRequest as CategoryStoreRequest;
use App\Http\Requests\Admin\Category\UpdateRequest;
use App\Http\Resources\Category\CategoryTreeResource;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

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

        return (new CategoryResource($category))->additional(['message' => 'Category has been created successfully'])->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::with('mainCategory')->find($id);

        if (is_null($category))
        {
            return response()->json(['message' => 'No category exists with the provided id'], Response::HTTP_NOT_FOUND);
        }

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $category = Category::find($id);;
        if (is_null($category))
        {
            return response()->json(['message' => 'No category exists with the provided id'], Response::HTTP_NOT_FOUND);
        }

        // Add updated by columns
        $policy = ['updated_by' => auth()->user()->id];

        $category->update(
            $request->only(['name', 'active', 'parent_id']) + $policy
        );

        return (new CategoryResource($category))->additional(['message' => 'Category has been updated successfully'])->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (is_null($category))
        {
            return response()->json(['message' => 'No category exists with the provided id'], Response::HTTP_NOT_FOUND);
        }

        $category->delete();

        return (new CategoryResource($category))->additional(['message' => 'Category has been deleted successfully'])->response()->setStatusCode(Response::HTTP_OK);
    }

    public function getAsTree()
    {
        return  CategoryTreeResource::collection(Category::treeify());
    }
}
