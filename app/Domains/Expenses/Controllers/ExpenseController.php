<?php

namespace App\Domains\Expenses\Controllers;

use App\Domains\Auth\Models\ActivityLog;
use App\Domains\Auth\Models\Tenant;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index(Request $request)
    {
        $query = Expense::with('category')
            ->where('tenant_id', Auth::user()->tenant_id);

        if ($categoryId = $request->get('category_id')) {
            $query->where('expense_category_id', $categoryId);
        }

        if ($from = $request->get('date_from')) {
            $query->whereDate('date', '>=', $from);
        }

        if ($to = $request->get('date_to')) {
            $query->whereDate('date', '<=', $to);
        }

        if ($paymentMethod = $request->get('payment_method')) {
            $query->where('payment_method', $paymentMethod);
        }

        $expenses = $query->latest()->paginate(15);
        $categories = ExpenseCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('expenses.index', compact('expenses', 'categories', 'exchangeRate'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:1000',
            'date' => 'required|date',
            'payment_method' => 'required|in:cash,card,transfer,other',
            'receipt' => 'nullable|image|max:2048',
        ]);

        $expense = Expense::create([
            'tenant_id' => Auth::user()->tenant_id,
            'expense_category_id' => $validated['expense_category_id'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'payment_method' => $validated['payment_method'],
            'created_by' => Auth::id(),
        ]);

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('expenses/receipts', 'public');
            $expense->update(['receipt' => $path]);
        }

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'create_expense',
            'description' => 'تم إضافة مصروف بقيمة ' . number_format($validated['amount'], 2),
            'subject_type' => Expense::class,
            'subject_id' => $expense->id,
        ]);

        session()->flash('success', 'تم إضافة المصروف بنجاح');

        return redirect()->route('expenses.index');
    }

    public function edit($id)
    {
        $expense = Expense::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $categories = ExpenseCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:1000',
            'date' => 'required|date',
            'payment_method' => 'required|in:cash,card,transfer,other',
            'receipt' => 'nullable|image|max:2048',
        ]);

        $expense->update($validated);

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('expenses/receipts', 'public');
            $expense->update(['receipt' => $path]);
        }

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'update_expense',
            'description' => 'تم تحديث المصروف رقم ' . $expense->id,
            'subject_type' => Expense::class,
            'subject_id' => $expense->id,
        ]);

        session()->flash('success', 'تم تحديث المصروف بنجاح');

        return redirect()->route('expenses.index');
    }

    public function destroy($id)
    {
        $expense = Expense::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $expense->delete();

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'delete_expense',
            'description' => 'تم حذف المصروف رقم ' . $expense->id,
            'subject_type' => Expense::class,
            'subject_id' => $expense->id,
        ]);

        session()->flash('success', 'تم حذف المصروف بنجاح');

        return redirect()->route('expenses.index');
    }
}
