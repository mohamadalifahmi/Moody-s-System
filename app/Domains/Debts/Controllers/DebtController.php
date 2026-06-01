<?php

namespace App\Domains\Debts\Controllers;

use App\Domains\Debts\Models\Debt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index()
    {
        $debts = Debt::where('tenant_id', Auth::user()->tenant_id)
            ->latest()
            ->paginate(15);

        $tenant = Auth::user()->tenant;
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('debts.index', compact('debts', 'exchangeRate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'creditor_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string|in:active,settled',
            'notes' => 'nullable|string|max:2000',
        ]);

        Debt::create([
            'tenant_id' => Auth::user()->tenant_id,
            'creditor_name' => $validated['creditor_name'],
            'amount' => $validated['amount'],
            'paid_amount' => $validated['paid_amount'] ?? 0,
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'notes' => $validated['notes'] ?? null,
        ]);

        session()->flash('success', 'تم إضافة الدين بنجاح');

        return redirect()->route('debts.index');
    }

    public function update(Request $request, $id)
    {
        $debt = Debt::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'creditor_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string|in:active,settled',
            'notes' => 'nullable|string|max:2000',
        ]);

        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $debt->update($validated);

        session()->flash('success', 'تم تحديث الدين بنجاح');

        return redirect()->route('debts.index');
    }

    public function destroy($id)
    {
        $debt = Debt::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $debt->delete();

        session()->flash('success', 'تم حذف الدين بنجاح');

        return redirect()->route('debts.index');
    }
}
