<?php

namespace App\Domains\Search\Controllers;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Inventory\Models\Product;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Sales\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index(Request $request)
    {
        $q = $request->get('q');

        if (!$q || trim($q) === '') {
            return redirect()->back();
        }

        $tenantId = Auth::user()->tenant_id;

        $products = Product::where('tenant_id', $tenantId)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get();

        $suppliers = Supplier::where('tenant_id', $tenantId)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get();

        $orders = Order::where('tenant_id', $tenantId)
            ->where('order_number', 'like', "%{$q}%")
            ->limit(10)
            ->get();

        $expenses = Expense::where('tenant_id', $tenantId)
            ->where('description', 'like', "%{$q}%")
            ->limit(10)
            ->get();

        $tenant = \App\Domains\Auth\Models\Tenant::find($tenantId);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('search.index', compact(
            'q', 'products', 'suppliers', 'orders', 'expenses', 'exchangeRate'
        ));
    }
}
