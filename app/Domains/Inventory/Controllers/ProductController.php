<?php

namespace App\Domains\Inventory\Controllers;

use App\Domains\Auth\Models\Tenant;
use App\Domains\Inventory\Models\Product;
use App\Domains\Inventory\Models\ProductCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('tenant_id', Auth::user()->tenant_id);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->get('stock_status') === 'low') {
            $query->where('stock_quantity', '<', 10);
        }

        $products = $query->latest()->paginate(15);
        $categories = ProductCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('inventory.products.index', compact('products', 'categories', 'exchangeRate'));
    }

    public function create()
    {
        $categories = ProductCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('inventory.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validated['sale_price'] < $validated['purchase_price']) {
            session()->flash('error', 'سعر البيع يجب أن يكون أكبر من أو يساوي سعر الشراء');
            return redirect()->back()->withInput();
        }

        Product::create([
            'tenant_id' => Auth::user()->tenant_id,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?? null,
            'barcode' => $validated['barcode'] ?? null,
            'purchase_price' => $validated['purchase_price'],
            'sale_price' => $validated['sale_price'],
            'stock_quantity' => $validated['stock_quantity'],
            'unit' => $validated['unit'],
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => Auth::id(),
        ]);

        session()->flash('success', 'تم إضافة المنتج بنجاح');

        return redirect()->route('inventory.products.index');
    }

    public function edit($id)
    {
        $product = Product::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $categories = ProductCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('inventory.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $id,
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validated['sale_price'] < $validated['purchase_price']) {
            session()->flash('error', 'سعر البيع يجب أن يكون أكبر من أو يساوي سعر الشراء');
            return redirect()->back()->withInput();
        }

        $product->update($validated);

        session()->flash('success', 'تم تحديث المنتج بنجاح');

        return redirect()->route('inventory.products.index');
    }

    public function destroy($id)
    {
        $product = Product::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        if ($product->orderItems()->count() > 0 || $product->invoiceItems()->count() > 0) {
            session()->flash('error', 'لا يمكن حذف المنتج لوجوده في طلبيات أو فواتير');
            return redirect()->route('inventory.products.index');
        }

        $product->delete();

        session()->flash('success', 'تم حذف المنتج بنجاح');

        return redirect()->route('inventory.products.index');
    }
}
