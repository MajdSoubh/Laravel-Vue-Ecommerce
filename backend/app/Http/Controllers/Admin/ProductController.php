<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Product\StoreRequest;
use App\Http\Requests\Admin\Product\UpdateRequest;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Traits\ImageHelper;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    use ImageHelper;

    /**
     * Display a paginated list of products with optional search, sorting, and pagination.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');
        $perPage = request('per_page', '10');

        $products = Product::where('title', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created product in storage, including images and category associations.
     *
     * @param \App\Http\Requests\Admin\Product\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        // Add created_by and updated_by columns
        $policy = ['created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id];

        // Create the product
        $product = Product::create($request->except(['images', 'categories']) + $policy);

        // Associate categories
        $product->categories()->sync($request->input('categories'));

        // Store images
        if ($request->hasFile('images') && count($request->file('images')))
        {
            $this->saveImages($product, $request->file('images'));
        }

        return (new ProductResource($product))
            ->additional(['message' => __('product.created')])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the details of a specific product by its ID.
     *
     * @param string $id The unique identifier of the product.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (is_null($product))
        {
            return response()->json(
                ['message' => __('product.not_found')],
                Response::HTTP_NOT_FOUND
            );
        }

        return new ProductResource($product);
    }

    /**
     * Update the specified product in storage, including images, categories, and other attributes.
     *
     * @param \App\Http\Requests\Admin\Product\UpdateRequest $request
     * @param string $id The unique identifier of the product.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, string $id)
    {
        $product = Product::find($id);

        if (is_null($product))
        {
            return response()->json(
                ['message' => __('product.not_found')],
                Response::HTTP_NOT_FOUND
            );
        }

        // Update images if provided
        if ($request->hasFile('images') && count($request->file('images')))
        {
            $this->deleteImages($product); // Delete previous images
            $this->saveImages($product, $request->file('images')); // Save new images
        }

        // Update categories if provided
        if ($request->has('categories'))
        {
            $product->categories()->sync($request->input('categories'));
        }

        // Update the updated_by column
        $policy = ['updated_by' => auth()->user()->id];

        // Update the product
        $product->update($request->except(['categories', 'images']) + $policy);

        return (new ProductResource($product))
            ->additional(['message' => __('product.updated')])
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified product from storage, including associated images and categories.
     *
     * @param string $id The unique identifier of the product.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (is_null($product))
        {
            return response()->json(
                ['message' => __('product.not_found')],
                Response::HTTP_NOT_FOUND
            );
        }

        // Delete associated images
        $this->deleteImages($product);

        // Detach categories
        $product->categories()->detach();

        // Delete the product
        $product->delete();

        return (new ProductResource($product))
            ->additional(['message' => __('product.deleted')])
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
