<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display a paginated list of orders with associated details, items, products, images, and client information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $perPage = request('per_page', '10');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'asc');

        $orders = Order::with(['details', 'items.product.images', 'client'])
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return OrderResource::collection($orders);
    }

    /**
     * Display the details of a specific order by its ID.
     *
     * @param string $id The unique identifier of the order.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $order = Order::with(['details', 'items.product.images'])->find($id);

        // Check if the order exists
        if (is_null($order))
        {
            return response()->json(
                ['message' => __('order.not_found')],
                Response::HTTP_NOT_FOUND
            );
        }

        // Check if the authenticated user has permission to access the order
        if ($order->created_by != request()->user()->id)
        {
            return response()->json(
                ['message' => __('order.unauthorized')],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return new OrderResource($order);
    }
}
