<?php

namespace App\Domains\Inventory\Controllers;

use App\Domains\Inventory\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index(Request $request)
    {
        $query = Supplier::where('tenant_id', Auth::user()->tenant_id);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->latest()->paginate(15);

        return view('inventory.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        Supplier::create([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => Auth::id(),
        ]);

        session()->flash('success', 'تم إضافة المورد بنجاح');

        return redirect()->route('inventory.suppliers.index');
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $supplier->update($validated);

        session()->flash('success', 'تم تحديث بيانات المورد بنجاح');

        return redirect()->route('inventory.suppliers.index');
    }

    public function destroy($id)
    {
        $supplier = Supplier::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $supplier->delete();

        session()->flash('success', 'تم حذف المورد بنجاح');

        return redirect()->route('inventory.suppliers.index');
    }
}
