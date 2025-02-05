<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\UserTypes;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class DashboardController extends Controller
{
    /**
     * Retrieve dashboard statistics including active customers, products, orders, income, and more.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $activeCustomers = $this->activeCustomers();
        $activeProducts = $this->activeProducts();
        $ordersCount = $this->paidOrders();
        $totalIncome = $this->totalIncome();
        $mostRequestedProducts = $this->mostReqestedProdcuts();
        $latestCustomers = $this->latestCustomers();
        $latestOrders = $this->latestOrders();

        return response()->json([
            'activeCustomer' => $activeCustomers,
            'activeProducts' => $activeProducts,
            'ordersCount' => $ordersCount,
            'totalIncome' => $totalIncome,
            'mostRequestedProducts' => $mostRequestedProducts,
            'latestCustomer' => $latestCustomers,
            'latestOrders' => OrderResource::collection($latestOrders),
        ]);
    }

    /**
     * Retrieve the most requested products within the specified period.
     *
     * @return \stdClass
     */
    public function mostReqestedProdcuts()
    {
        $mostRequestedProducts = OrderItem::with('product')
            ->where('created_at', '>=', $this->period())
            ->select([DB::raw('SUM(quantity) as quantity'), DB::raw('product_id')])
            ->groupBy('product_id')
            ->get();

        $mostRequestedProducts = $mostRequestedProducts->map(function ($product)
        {
            $product->title = $product->product->title;
            return $product;
        });

        $mostRequestedProducts = $mostRequestedProducts->sortByDesc('quantity')->slice(0, 5);

        $_mostRequestedProducts = new stdClass();
        $_mostRequestedProducts->titles = $mostRequestedProducts->pluck('title');
        $_mostRequestedProducts->quantity = $mostRequestedProducts->pluck('quantity');

        return $_mostRequestedProducts;
    }

    /**
     * Count the number of active customers within the specified period.
     *
     * @return int
     */
    public function activeCustomers()
    {
        return User::where('type', UserTypes::client->value)
            ->where('active', 1)
            ->where('created_at', '>=', $this->period())
            ->count();
    }

    /**
     * Count the number of active products within the specified period.
     *
     * @return int
     */
    public function activeProducts()
    {
        return Product::where('published', '=', 1)
            ->where('created_at', '>=', $this->period())
            ->count();
    }

    /**
     * Count the number of paid orders within the specified period.
     *
     * @return int
     */
    public function paidOrders()
    {
        return Order::where('status', OrderStatus::Paid->value)
            ->where('created_at', '>=', $this->period())
            ->count();
    }

    /**
     * Calculate the total income from paid orders within the specified period.
     *
     * @return float
     */
    public function totalIncome()
    {
        $totalIncome = Order::where('status', OrderStatus::Paid->value)
            ->where('created_at', '>=', $this->period())
            ->sum('total_price');

        return round($totalIncome);
    }

    /**
     * Retrieve the latest active customers within the specified period.
     *
     * @return \Illuminate\Support\Collection
     */
    public function latestCustomers()
    {
        return User::where('type', UserTypes::client->value)
            ->where('active', 1)
            ->where('created_at', '>=', $this->period())
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
    }

    /**
     * Retrieve the latest orders within the specified period.
     *
     * @return \Illuminate\Support\Collection
     */
    public function latestOrders()
    {
        return Order::latest()
            ->where('created_at', '>=', $this->period())
            ->with('client')
            ->withCount('items')
            ->limit(5)
            ->get();
    }

    /**
     * Determine the time period based on the request input.
     *
     * @return \Illuminate\Support\Carbon
     */
    private function period()
    {
        $period = match (request()->input('period', '%'))
        {
            '1d' => Carbon::now()->subDay(),
            '1w' => Carbon::now()->subWeek(),
            '2w' => Carbon::now()->subWeeks(2),
            '1m' => Carbon::now()->subMonth(1),
            '3m' => Carbon::now()->subMonths(3),
            '6m' => Carbon::now()->subMonths(6),
            'all' => Carbon::now()->subYears(9),
            '%' => Carbon::now()->subYears(9),
        };

        return $period;
    }
}
