<?php

namespace App\Domains\Inventory\Controllers;

use App\Domains\Auth\Models\ActivityLog;
use App\Domains\Auth\Models\Tenant;
use App\Domains\Inventory\Models\Product;
use App\Domains\Inventory\Models\Purchase;
use App\Domains\Inventory\Models\StockMovement;
use App\Domains\Inventory\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index(Request $request)
    {
        $query = Purchase::with('supplier')
            ->where('tenant_id', Auth::user()->tenant_id);

        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->get('date_from')) {
            $query->whereDate('date', '>=', $from);
        }

        if ($to = $request->get('date_to')) {
            $query->whereDate('date', '<=', $to);
        }

        $purchases = $query->latest()->paginate(15);
        $suppliers = Supplier::where('tenant_id', Auth::user()->tenant_id)->get();

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('inventory.purchases.index', compact('purchases', 'suppliers', 'exchangeRate'));
    }

    public function create()
    {
        $suppliers = Supplier::where('tenant_id', Auth::user()->tenant_id)->get();
        $products = Product::with('category')
            ->where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->get();

        return view('inventory.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'invoice_no' => 'nullable|string|max:255',
            'paid' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            $tenantId = Auth::user()->tenant_id;
            $total = 0;
            $purchaseItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::where('tenant_id', $tenantId)
                    ->where('id', $item['product_id'])
                    ->firstOrFail();

                $itemTotal = $item['quantity'] * $item['unit_price'];
                $total += $itemTotal;

                $purchaseItems[] = new \App\Domains\Inventory\Models\PurchaseItem([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $itemTotal,
                ]);
            }

            $paid = $validated['paid'] ?? 0;
            $due = $total - $paid;

            $purchase = Purchase::create([
                'tenant_id' => $tenantId,
                'supplier_id' => $validated['supplier_id'],
                'invoice_no' => $validated['invoice_no'] ?? null,
                'total' => $total,
                'paid' => $paid,
                'due' => $due,
                'status' => $paid >= $total ? 'paid' : 'pending',
                'date' => $validated['date'],
                'created_by' => Auth::id(),
            ]);

            $purchase->items()->saveMany($purchaseItems);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $product->increment('stock_quantity', $item['quantity']);

                StockMovement::create([
                    'tenant_id' => $tenantId,
                    'product_id' => $item['product_id'],
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'notes' => 'مشتريات - فاتورة رقم ' . ($validated['invoice_no'] ?? $purchase->id),
                    'created_by' => Auth::id(),
                ]);
            }

            ActivityLog::create([
                'tenant_id' => $tenantId,
                'user_id' => Auth::id(),
                'action' => 'create_purchase',
                'description' => 'تم إنشاء فاتورة مشتريات بقيمة ' . number_format($total, 2),
                'subject_type' => Purchase::class,
                'subject_id' => $purchase->id,
            ]);

            session()->flash('success', 'تم إنشاء فاتورة المشتريات بنجاح');

            return redirect()->route('inventory.purchases.show', $purchase->id);
        });
    }

    public function edit($id)
    {
        $purchase = Purchase::with('items')
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $suppliers = Supplier::where('tenant_id', Auth::user()->tenant_id)->get();
        $products = Product::with('category')
            ->where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->get();

        return view('inventory.purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function show($id)
    {
        $purchase = Purchase::with(['items.product', 'supplier'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('inventory.purchases.show', compact('purchase', 'exchangeRate'));
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::with('items')
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'status' => 'nullable|in:pending,received,paid,cancelled',
            'paid' => 'nullable|numeric|min:0',
            'invoice_no' => 'nullable|string|max:255',
        ]);

        $updateData = [
            'supplier_id' => $validated['supplier_id'],
            'date' => $validated['date'],
            'invoice_no' => $validated['invoice_no'] ?? $purchase->invoice_no,
        ];

        if (isset($validated['paid'])) {
            $updateData['paid'] = $validated['paid'];
            $updateData['due'] = $purchase->total - $validated['paid'];
        }

        if (isset($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }

        $purchase->update($updateData);

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'update_purchase',
            'description' => 'تم تحديث فاتورة المشتريات رقم ' . $purchase->id,
            'subject_type' => Purchase::class,
            'subject_id' => $purchase->id,
        ]);

        session()->flash('success', 'تم تحديث فاتورة المشتريات بنجاح');

        return redirect()->route('inventory.purchases.show', $id);
    }

    public function destroy($id)
    {
        $purchase = Purchase::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        if ($purchase->status !== 'pending') {
            session()->flash('error', 'يمكن حذف المشتريات المعلقة فقط');
            return redirect()->route('inventory.purchases.index');
        }

        $purchase->delete();

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'delete_purchase',
            'description' => 'تم حذف فاتورة المشتريات رقم ' . $purchase->id,
            'subject_type' => Purchase::class,
            'subject_id' => $purchase->id,
        ]);

        session()->flash('success', 'تم حذف فاتورة المشتريات بنجاح');

        return redirect()->route('inventory.purchases.index');
    }
}
