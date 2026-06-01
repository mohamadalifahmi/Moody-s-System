<?php

namespace App\Domains\Auth\Controllers;

use App\Domains\Auth\Models\Tenant;
use App\Domains\Auth\Models\User;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Inventory\Models\Product;
use App\Domains\Sales\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $today = now()->startOfDay();

        $todaySales = Order::where('tenant_id', $tenantId)
            ->whereDate('created_at', $today)
            ->get();

        $todaySalesCount = $todaySales->count();
        $todaySalesTotal = $todaySales->sum('total');

        $todayExpensesTotal = Expense::where('tenant_id', $tenantId)
            ->whereDate('date', $today)
            ->sum('amount');

        $activeOrdersCount = Order::where('tenant_id', $tenantId)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->count();

        $lowStockProductsCount = Product::where('tenant_id', $tenantId)
            ->where('stock_quantity', '<=', 5)
            ->count();

        $recentOrders = Order::where('tenant_id', $tenantId)
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        $tenant = Tenant::find($tenantId);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('dashboard', compact(
            'todaySalesCount',
            'todaySalesTotal',
            'todayExpensesTotal',
            'activeOrdersCount',
            'lowStockProductsCount',
            'recentOrders',
            'exchangeRate'
        ));
    }
}
