<?php

namespace App\Domains\Invoicing\Controllers;

use App\Domains\Auth\Models\ActivityLog;
use App\Domains\Auth\Models\Tenant;
use App\Domains\Inventory\Models\Product;
use App\Domains\Invoicing\Models\Invoice;
use App\Domains\Invoicing\Models\InvoiceItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index(Request $request)
    {
        $query = Invoice::with('order')
            ->where('tenant_id', Auth::user()->tenant_id);

        if ($search = $request->get('search')) {
            $query->where('invoice_number', 'like', "%{$search}%");
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

        $invoices = $query->latest()->paginate(15);

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('invoicing.invoices.index', compact('invoices', 'exchangeRate'));
    }

    public function create()
    {
        $products = Product::with('category')
            ->where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->get();

        return view('invoicing.invoices.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated) {
            $tenantId = Auth::user()->tenant_id;
            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::where('tenant_id', $tenantId)
                    ->where('id', $item['product_id'])
                    ->firstOrFail();

                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $discount = $validated['discount'] ?? 0;
            $taxRate = 0;
            $tax = $subtotal * $taxRate;
            $total = $subtotal - $discount + $tax;

            $today = now()->format('Ymd');
            $lastInvoice = Invoice::where('tenant_id', $tenantId)
                ->whereDate('created_at', today())
                ->count();

            $invoiceNumber = 'INV-' . $today . '-' . str_pad($lastInvoice + 1, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'tenant_id' => $tenantId,
                'invoice_number' => $invoiceNumber,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'paid' => 0,
                'due' => $total,
                'status' => 'issued',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $itemTotal = $item['quantity'] * $item['unit_price'];

                InvoiceItem::create([
                    'tenant_id' => $tenantId,
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $itemTotal,
                ]);
            }

            ActivityLog::create([
                'tenant_id' => $tenantId,
                'user_id' => Auth::id(),
                'action' => 'create_invoice',
                'description' => 'تم إنشاء فاتورة جديدة رقم ' . $invoiceNumber,
                'subject_type' => Invoice::class,
                'subject_id' => $invoice->id,
            ]);

            session()->flash('success', 'تم إنشاء الفاتورة بنجاح');

            return redirect()->route('invoicing.invoices.show', $invoice->id);
        });
    }

    public function show($id)
    {
        $invoice = Invoice::with(['items.product', 'payments', 'order'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('invoicing.invoices.show', compact('invoice', 'exchangeRate'));
    }

    public function edit($id)
    {
        $invoice = Invoice::with('items')
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        if (!in_array($invoice->status, ['draft', 'issued'])) {
            session()->flash('error', 'لا يمكن تعديل فاتورة بهذه الحالة');
            return redirect()->route('invoicing.invoices.show', $id);
        }

        $products = Product::with('category')
            ->where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->get();

        return view('invoicing.invoices.edit', compact('invoice', 'products'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        if (!in_array($invoice->status, ['draft', 'issued'])) {
            session()->flash('error', 'لا يمكن تعديل فاتورة بهذه الحالة');
            return redirect()->route('invoicing.invoices.show', $id);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:draft,issued,paid,cancelled',
        ]);

        $invoice->update($validated);

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'update_invoice',
            'description' => 'تم تحديث الفاتورة رقم ' . $invoice->invoice_number,
            'subject_type' => Invoice::class,
            'subject_id' => $invoice->id,
        ]);

        session()->flash('success', 'تم تحديث الفاتورة بنجاح');

        return redirect()->route('invoicing.invoices.show', $id);
    }

    public function destroy($id)
    {
        $invoice = Invoice::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $invoice->delete();

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'delete_invoice',
            'description' => 'تم حذف الفاتورة رقم ' . $invoice->invoice_number,
            'subject_type' => Invoice::class,
            'subject_id' => $invoice->id,
        ]);

        session()->flash('success', 'تم حذف الفاتورة بنجاح');

        return redirect()->route('invoicing.invoices.index');
    }

    public function print($id)
    {
        $invoice = Invoice::with(['items.product', 'payments', 'order'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('invoicing.invoices.print', compact('invoice', 'exchangeRate'));
    }

    public function markAsPaid($id)
    {
        $invoice = Invoice::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $invoice->update([
            'status' => 'paid',
            'paid' => $invoice->total,
            'due' => 0,
        ]);

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'mark_invoice_paid',
            'description' => 'تم تحديد الفاتورة رقم ' . $invoice->invoice_number . ' كمدفوعة',
            'subject_type' => Invoice::class,
            'subject_id' => $invoice->id,
        ]);

        session()->flash('success', 'تم تحديد الفاتورة كمدفوعة');

        return redirect()->route('invoicing.invoices.show', $id);
    }

    public function markAsCancelled($id)
    {
        $invoice = Invoice::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $invoice->update(['status' => 'cancelled']);

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'cancel_invoice',
            'description' => 'تم إلغاء الفاتورة رقم ' . $invoice->invoice_number,
            'subject_type' => Invoice::class,
            'subject_id' => $invoice->id,
        ]);

        session()->flash('success', 'تم إلغاء الفاتورة');

        return redirect()->route('invoicing.invoices.show', $id);
    }
}
