<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\City;
use App\Models\User;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Enums\OrderStatus;
use App\Models\CityVendor;
use Illuminate\Http\Request;
use App\Enums\VendorStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $orderPlaced = Order::count();
        $earnings = Order::sum('total_price');
        $passengersCount = Customer::count();

        $orderStats = [
            'pending' => Order::where('status', OrderStatus::pending)->count(),
            'completed' => Order::where('status', OrderStatus::approved)->count(),
            'canceled' => Order::where('status', OrderStatus::rejected)->count(),
        ];

        $topServices = DB::table('order_addon_service')
            ->select('addon_service_id', DB::raw('SUM(count) as total_usage'))
            ->groupBy('addon_service_id')
            ->orderByDesc('total_usage')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $service = \App\Models\AddonService::find($item->addon_service_id);
                return [
                    'name' => $service?->name ?? 'N/A',
                    'total_usage' => $item->total_usage,
                ];
            });

        $monthlyEarnings = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_price) as total")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        return view('welcome', compact(
            'orderPlaced',
            'earnings',
            'passengersCount',
            'orderStats',
            'topServices',
            'monthlyEarnings'
        ));
    }


}
