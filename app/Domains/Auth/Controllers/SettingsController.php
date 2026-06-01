<?php

namespace App\Domains\Auth\Controllers;

use App\Domains\Auth\Models\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
