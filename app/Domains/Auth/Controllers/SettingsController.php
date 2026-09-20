<?php

namespace App\Domains\Auth\Controllers;

use App\Domains\Auth\Models\ActivityLog;
use App\Domains\Auth\Models\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index()
    {
        $settings = Tenant::findOrFail(Auth::user()->tenant_id);

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $tenant = Tenant::findOrFail(Auth::user()->tenant_id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'business_type' => 'nullable|string|in:' . implode(',', array_keys(config('business.types', ['general']))),
            'currency' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048',
            'settings' => 'nullable|array',
            'settings.tax_rate' => 'nullable|numeric|min:0|max:100',
            'settings.exchange_rate' => 'nullable|numeric|min:0',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'] ?? $tenant->email,
            'phone' => $validated['phone'] ?? $tenant->phone,
            'address' => $validated['address'] ?? $tenant->address,
            'business_type' => $validated['business_type'] ?? $tenant->business_type ?? 'general',
            'currency' => $validated['currency'] ?? $tenant->currency,
            'timezone' => $validated['timezone'] ?? $tenant->timezone,
        ];

        if (isset($validated['settings'])) {
            $settings = $tenant->settings ?? [];
            $updateData['settings'] = array_merge($settings, $validated['settings']);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('tenants/logos', 'public');
            $updateData['logo'] = $path;
        }

        $tenant->update($updateData);

        session()->flash('success', 'تم تحديث الإعدادات بنجاح');

        return redirect()->route('settings.index');
    }

    public function clearAllData(Request $request)
    {
        $tenantId = (int) Auth::user()->tenant_id;

        $confirmed = $request->input('confirm_text');
        $tenant = Tenant::findOrFail($tenantId);

        if ($confirmed !== $tenant->name) {
            session()->flash('error', 'لم يتم المسح: يجب كتابة اسم المنشأة بشكل صحيح للتأكيد');
            return redirect()->route('settings.index');
        }

        DB::transaction(function () use ($tenantId) {
            $orderIds = DB::table('orders')->where('tenant_id', $tenantId)->pluck('id');
            $invoiceIds = DB::table('invoices')->where('tenant_id', $tenantId)->pluck('id');
            $purchaseIds = DB::table('purchases')->where('tenant_id', $tenantId)->pluck('id');

            DB::table('order_items')->whereIn('order_id', $orderIds)->delete();
            DB::table('invoice_items')->whereIn('invoice_id', $invoiceIds)->delete();
            DB::table('purchase_items')->whereIn('purchase_id', $purchaseIds)->delete();
            DB::table('payments')->where('tenant_id', $tenantId)->delete();

            DB::table('stock_movements')->where('tenant_id', $tenantId)->delete();
            DB::table('invoices')->where('tenant_id', $tenantId)->delete();
            DB::table('orders')->where('tenant_id', $tenantId)->delete();
            DB::table('order_sessions')->where('tenant_id', $tenantId)->delete();
            DB::table('purchases')->where('tenant_id', $tenantId)->delete();
            DB::table('debts')->where('tenant_id', $tenantId)->delete();
            DB::table('expenses')->where('tenant_id', $tenantId)->delete();
            DB::table('expense_categories')->where('tenant_id', $tenantId)->delete();
            DB::table('products')->where('tenant_id', $tenantId)->delete();
            DB::table('product_categories')->where('tenant_id', $tenantId)->delete();
            DB::table('suppliers')->where('tenant_id', $tenantId)->delete();
            DB::table('activity_logs')->where('tenant_id', $tenantId)->delete();
        });

        ActivityLog::create([
            'tenant_id' => $tenantId,
            'user_id' => Auth::id(),
            'action' => 'clear_all_data',
            'description' => 'تم مسح جميع بيانات المنشأة',
        ]);

        session()->flash('success', 'تم مسح جميع البيانات بنجاح');

        return redirect()->route('settings.index');
    }
}
