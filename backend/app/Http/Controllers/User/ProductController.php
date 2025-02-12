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
        $search = request('search', '');
        $perPage = request('per_page', '10');
        $categories = request('categories', false);

        $products = Product::published()
            ->where('title', 'like', "%{$search}%")
            ->when($categories, function ($query) use ($categories)
            {
                return $query->whereHas('categories', function (Builder $query) use ($categories)
                {
                    // Return only activated category's products
                    $query->where('active', true);

                    // Else return only selected ones.
                    return $query->whereIn('name', $categories)
                        ->orWhereHas('mainCategory', function (Builder $query) use ($categories)
                        {
                            // Return only activated category's products
                            $query->where('active', true);

                            return $query->whereIn('name', $categories);
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

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
