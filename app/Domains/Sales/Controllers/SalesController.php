<?php

namespace App\Domains\Sales\Controllers;

use App\Domains\Auth\Models\ActivityLog;
use App\Domains\Auth\Models\Tenant;
use App\Domains\Inventory\Models\Product;
use App\Domains\Sales\Models\Order;
use App\Domains\Sales\Models\OrderItem;
use App\Domains\Sales\Models\OrderSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'session', 'items'])
            ->where('tenant_id', Auth::user()->tenant_id);

        if ($search = $request->get('search')) {
            $query->where('order_number', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->paginate(15);

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('sales.orders.index', compact('orders', 'exchangeRate'));
    }

    public function create()
    {
        $categories = \App\Domains\Inventory\Models\ProductCategory::with(['products' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        $openSession = OrderSession::where('tenant_id', Auth::user()->tenant_id)
            ->where('status', 'open')
            ->latest()
            ->first();

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('sales.orders.create', compact('categories', 'openSession', 'exchangeRate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:order_sessions,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated) {
            $tenantId = Auth::user()->tenant_id;
            $subtotal = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::where('tenant_id', $tenantId)
                    ->where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = $item['quantity'];
                $unitPrice = $product->sale_price;
                $total = $quantity * $unitPrice;
                $subtotal += $total;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $total,
                ];

                $product->decrement('stock_quantity', $quantity);
            }

            $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $order = Order::create([
                'tenant_id' => $tenantId,
                'session_id' => $validated['session_id'],
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'tax' => 0,
                'discount' => 0,
                'total' => $subtotal,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            ActivityLog::create([
                'tenant_id' => $tenantId,
                'user_id' => Auth::id(),
                'action' => 'create_order',
                'description' => 'تم إنشاء طلب جديد رقم ' . $order->order_number,
                'subject_type' => Order::class,
                'subject_id' => $order->id,
            ]);

            session()->flash('success', 'تم إنشاء الطلب بنجاح');

            return redirect()->route('sales.orders.show', $order->id);
        });
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'payments', 'user', 'session'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('sales.orders.show', compact('order', 'exchangeRate'));
    }

    public function edit($id)
    {
        $order = Order::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        if ($order->status === 'cancelled') {
            session()->flash('error', 'لا يمكن تعديل طلب ملغي');
            return redirect()->route('sales.orders.show', $id);
        }

        $categories = \App\Domains\Inventory\Models\ProductCategory::with(['products' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('sales.orders.edit', compact('order', 'categories', 'exchangeRate'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        if ($order->status === 'cancelled') {
            session()->flash('error', 'لا يمكن تعديل طلب ملغي');
            return redirect()->route('sales.orders.show', $id);
        }

        $validated = $request->validate([
            'status' => 'nullable|in:pending,preparing,ready,completed,cancelled',
            'payment_status' => 'nullable|in:unpaid,partial,paid',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update($validated);

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'update_order',
            'description' => 'تم تحديث الطلب رقم ' . $order->order_number,
            'subject_type' => Order::class,
            'subject_id' => $order->id,
        ]);

        session()->flash('success', 'تم تحديث الطلب بنجاح');

        return redirect()->route('sales.orders.show', $id);
    }

    public function destroy($id)
    {
        $order = Order::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        if ($order->status !== 'pending') {
            session()->flash('error', 'يمكن حذف الطلبات المعلقة فقط');
            return redirect()->route('sales.orders.index');
        }

        $order->delete();

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'delete_order',
            'description' => 'تم حذف الطلب رقم ' . $order->order_number,
            'subject_type' => Order::class,
            'subject_id' => $order->id,
        ]);

        session()->flash('success', 'تم حذف الطلب بنجاح');

        return redirect()->route('sales.orders.index');
    }

    public function print($id)
    {
        $order = Order::with(['items.product', 'payments', 'user', 'session'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('sales.orders.print', compact('order', 'exchangeRate'));
    }
}
