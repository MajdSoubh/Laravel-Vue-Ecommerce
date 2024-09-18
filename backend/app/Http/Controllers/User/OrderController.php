<?php

namespace App\Http\Controllers\User;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\Order\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = request('per_page', '10');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'asc');
        $orders = Order::with(['details', 'items'])->where('created_by', request()->user()->id)->orderBy($sortField, $sortDirection)->paginate($perPage);

        return OrderResource::collection($orders);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['details', 'items.product.images'])->find($id);

        if (is_null($order))
        {
            return response()->json(['message' => 'No order exists with the provided id'], HttpStatusCode::NOT_FOUND->value);
        }

        if ($order->created_by != request()->user()->id)
        {
            return response()->json(['message' => 'you don\'t have persmission to access this order'] . HttpStatusCode::UNAUTHORIZED);
        }

        return new OrderResource($order);
    }
}
