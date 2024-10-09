<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Product\StoreRequest as ProductStoreRequest;
use App\Http\Requests\Admin\Product\UpdateRequest as ProductUpdateRequest;
use App\Http\Resources\Admin\Product\ProductResource;
use App\Models\Product;
use App\Traits\ImageHelper;
use  App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    use ImageHelper;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');
        $perPage = request('per_page', '10');
        $products = Product::where('title', 'like', "%{$search}%")->orderBy($sortField, $sortDirection)->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {

        // Add created by, updated by columns
        $policy = ['created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id];

        // create the product
        $product = Product::create($request->except(['images', 'categories']) + $policy);

        // associate the category
        $product->Categories()->sync($request->input('categories'));

        // store the images
        $this->saveImages($product, $request->file('images'));

        return (new ProductResource($product))->additional(['message' => 'Product has been created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (is_null($product))
        {
            return response()->json(['message' => 'No product exists with the provided id'], Response::HTTP_NOT_FOUND);
        }

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, string $id)
    {

        $product = Product::find($id);

        // check if the product existed.
        if (is_null($product))
        {
            return response()->json(['message' => 'No product exists with the provided id'], Response::HTTP_NOT_FOUND);
        }

        // check if images should be updated
        if ($request->hasFile('images') && count($request->file('images')))
        {
            // delete previous images
            $this->deleteImages($product);

            // associate new images
            $this->saveImages($product, $request->file('images'));
        }

        // associate the category
        if ($request->has('categories'))
        {
            $product->Categories()->sync($request->input('categories'));
        }


        // Update the update_by column
        $policy = ['updated_by' => auth()->user()->id, 'updated_by' => auth()->user()->id];

        // Update the product
        $product->update($request->except(['categories', 'images']) + $policy);

        return (new ProductResource($product))->additional(['message' => 'Product has been updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (is_null($product))
        {
            return response()->json(['message' => 'No product exists with the provided id'], Response::HTTP_NOT_FOUND);
        }

        // delete associated images
        $this->deleteImages($product);

        // detach the categories
        $product->categories()->detach();

        // detele the product
        $product->delete();

        return new ProductResource($product);
    }
}
