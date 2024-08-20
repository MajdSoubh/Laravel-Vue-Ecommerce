<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class ReportController extends Controller
{

    public function index()
    {
        // Total amount of Orders by date
        $ordersPaid = Order::where('status', OrderStatus::Paid->value)->where('created_at', '>=', $this->period())->select([DB::raw('SUM(total_price) as amount'), DB::raw('CAST(created_at as DATE) AS day')])->groupBy('day')->get();
        $ordersPaid = $ordersPaid->keyBy('day');
        $now = Carbon::now()->addDay(1);
        $fromDate = $this->period();
        $dates = [];
        $data = [];
        while ($fromDate <= $now)
        {
            $date = $fromDate->format('Y-m-d');
            $date;
            $dates[] = $date;
            $data[] = isset($ordersPaid[$date]) ? $ordersPaid[$date]->amount : 0;
            $fromDate->addDay();
        }
        $ordersPaidChartData = new stdClass();
        $ordersPaidChartData->dates = $dates;
        $ordersPaidChartData->data = $data;

        // Most profitable products
        $OrdersItems = OrderItem::with('product')->where('created_at', '>=', $this->period())->select([DB::raw('SUM(quantity) as quantity'), DB::raw('product_id')])->groupBy('product_id')->get();
        $products = $OrdersItems->map(function ($orderItem)
        {
            $orderItem->amount = $orderItem->product->price * $orderItem->quantity;
            $orderItem->title = $orderItem->product->title;
            unset($orderItem->product);
            return $orderItem;
        });
        $products = $products->sortByDesc('amount');
        $products = $products->slice(0, 8);
        $items = new stdClass();
        $items->titles = $products->pluck('title');
        $items->amounts = $products->pluck('amount');

        return response()->json([
            'orders' => $ordersPaidChartData,
            'products' => $items,
        ]);
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
            'all' => Carbon::now()->subMonths(9),
            '%' => Carbon::now()->subMonths(9),
        };
        return $period;
    }
}
