<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $perPage = request('per_page', '10');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'asc');

        $orders = Order::with(['details', 'items'])
            ->where('created_by', request()->user()->id)
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return OrderResource::collection($orders);
    }

    /**
     * Display the specified order.
     *
     * @param string $id The ID of the order.
     * @return \App\Http\Resources\Order\OrderResource|\Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $order = Order::with(['details', 'items.product.images'])->find($id);

        // Check if the order exists
        if (is_null($order))
        {
            return response()->json(['message' => __('orders.not_found')], Response::HTTP_NOT_FOUND);
        }

        // Check if the authenticated user owns the order
        if ($order->created_by != request()->user()->id)
        {
            return response()->json(['message' => __('orders.unauthorized')], Response::HTTP_UNAUTHORIZED);
        }

        return new OrderResource($order);
    }
}
