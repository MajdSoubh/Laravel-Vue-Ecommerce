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

    public function index()
    {
        $activeCustomers = User::where('type', UserTypes::client->value)->where('active', 1)->where('created_at', '>=', $this->period())->count();
        $activeProducts = Product::where('published', '=', 1)->where('created_at', '>=', $this->period())->count();
        $ordersCount = Order::where('status', OrderStatus::Paid->value)->where('created_at', '>=', $this->period())->count();
        $totalIncome = Order::where('status', OrderStatus::Paid->value)->where('created_at', '>=', $this->period())->sum('total_price');
        $latestCustomer = User::where('type', UserTypes::client->value)->where('active', 1)->where('created_at', '>=', $this->period())->orderBy('created_at', 'desc')->limit(3)->get();
        $latestOrders = Order::latest()->where('created_at', '>=', $this->period())->with('client')->withCount('items')->limit(5)->get();

        return response()->json([
            'activeCustomer' => $activeCustomers,
            'activeProducts' => $activeProducts,
            'ordersCount' => $ordersCount,
            'totalIncome' => round($totalIncome),
            'mostRequestedProducts' => $this->mostReqestedProdcuts(),
            'latestCustomer' => $latestCustomer,
            'latestOrders' =>  OrderResource::collection($latestOrders),
        ]);
    }

    public function mostReqestedProdcuts()
    {
        $mostRequestedProducts = OrderItem::with('product')->where('created_at', '>=', $this->period())->select([DB::raw('SUM(quantity) as quantity'), DB::raw('product_id')])->groupBy('product_id')->get();
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
    public function activeCustomers()
    {
        return User::where('type', UserTypes::client->value)->where('active', 1)->where('created_at', '>=', $this->period())->count();
    }

    public function activeProducts()
    {
        return Product::where('published', '=', 1)->where('created_at', '>=', $this->period())->count();
    }

    public function paidOrders()
    {
        $ordersCount = Order::where('status', OrderStatus::Paid->value)->where('created_at', '>=', $this->period())->count();
        return  $ordersCount;
    }

    public function totalIncome()
    {
        $totalIncome = Order::where('status', OrderStatus::Paid->value)->where('created_at', '>=', $this->period())->sum('total_price');
        return  round($totalIncome);
    }
    public function productSales($limit = 5)
    {
        $products = Product::where('created_at', '>=', $this->period())->withCount('orders')->get("title");
    }


    public function latestCustomers()
    {
        return User::where('type', UserTypes::client->value)->where('active', 1)->where('created_at', '>=', $this->period())->orderBy('created_at', 'desc')->limit(3)->get();
    }

    public function latestOrders()
    {
        $latestOrder = Order::latest()->where('created_at', '>=', $this->period())->with('client')->withCount('items')->limit(5)->get();
        return $latestOrder;
    }

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
