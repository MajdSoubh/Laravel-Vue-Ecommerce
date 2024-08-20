<?php

namespace App\Http\Controllers\User;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\Product\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{
    public function index()
    {
        $search = request('search', '');
        $perPage = request('per_page', '10');
        $categories = request('categories', false);
        $products = Product::published()->where('title', 'like', "%{$search}%")->whereHas('categories', function (Builder $query) use ($categories)
        {
            // Return only activated category's  products
            $query = $query->where('active', true);

            // If no specific categories are selected return the query
            if (!$categories) return $query;

            // Else reutrn only selected ones.
            return $query->whereIn('name', $categories)->orWhereHas('mainCategory', function (Builder $query) use ($categories)
            {
                // Return only activated category's  products
                $query = $query->where('active', true);


                return $query->whereIn('name', $categories);
            });
        })->orderBy('created_at', 'desc')->paginate($perPage);




        return ProductResource::collection($products);
    }

    /**
     * Return the requested product by id or by slug
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
            return response()->json(['message' => 'No product exists with the provided data'], HttpStatusCode::NOT_FOUND->value);
        }

        return new ProductResource($result);
    }
}
