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
     * Display a paginated list of categories with optional search, sorting, and pagination.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'asc');
        $perPage = request('per_page', '10');

        $categories = Category::where('name', 'like', "%{$search}%")
            ->with('mainCategory')
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created category in storage.
     *
     * @param \App\Http\Requests\Admin\Category\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryStoreRequest $request)
    {
        // Add created_by and updated_by columns
        $policy = ['created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id];

        $category = Category::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent'),
            'active' => $request->input('active'),
        ] + $policy);

        return (new CategoryResource($category))
            ->additional(['message' => __('category.created')])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the details of a specific category by its ID.
     *
     * @param string $id The unique identifier of the category.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $category = Category::with('mainCategory')->find($id);

        if (is_null($category))
        {
            return response()->json(
                ['message' => __('category.not_found')],
                Response::HTTP_NOT_FOUND
            );
        }

        return new CategoryResource($category);
    }

    /**
     * Update the specified category in storage.
     *
     * @param \App\Http\Requests\Admin\Category\UpdateRequest $request
     * @param string $id The unique identifier of the category.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, string $id)
    {
        $category = Category::find($id);

        if (is_null($category))
        {
            return response()->json(
                ['message' => __('category.not_found')],
                Response::HTTP_NOT_FOUND
            );
        }

        // Add updated_by column
        $policy = ['updated_by' => auth()->user()->id];

        $category->update(
            $request->only(['name', 'active', 'parent_id']) + $policy
        );

        return (new CategoryResource($category))
            ->additional(['message' => __('category.updated')])
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified category from storage.
     *
     * @param string $id The unique identifier of the category.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (is_null($category))
        {
            return response()->json(
                ['message' => __('category.not_found')],
                Response::HTTP_NOT_FOUND
            );
        }

        $category->delete();

        return (new CategoryResource($category))
            ->additional(['message' => __('category.deleted')])
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Retrieve all categories in a tree structure.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAsTree()
    {
        return CategoryTreeResource::collection(Category::treeify());
    }
}
