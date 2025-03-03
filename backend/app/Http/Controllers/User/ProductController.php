<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a paginated list of published product.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $products = Product::published()
            ->filter([
                'search' => request('search', ''),
                'categories' => request('categories', [])
            ])
            ->orderByDesc('created_at')
            ->paginate(request('per_page', 10));

        return ProductResource::collection($products);
    }

    /**
     * Display the specified product by ID or slug.
     *
     * @param mixed $product The ID or slug of the product.
     * @return \App\Http\Resources\Product\ProductResource|\Illuminate\Http\JsonResponse
     */
    public function show($product)
    {
        $result = null;
        if (is_numeric($product))
        {
            $result = Product::find($product);
        }
        else
        {
            $result = Product::where('slug', $product)->first();
        }

        if (is_null($result))
        {
            return response()->json(['message' => __('product.not_found')], Response::HTTP_NOT_FOUND);
        }

        return new ProductResource($result);
    }
}
