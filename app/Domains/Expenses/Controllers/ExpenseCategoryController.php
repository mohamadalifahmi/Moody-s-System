<?php

namespace App\Domains\Expenses\Controllers;

use App\Domains\Expenses\Models\ExpenseCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')
            ->where('tenant_id', Auth::user()->tenant_id)
            ->latest()
            ->paginate(15);

        return view('expenses.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        ExpenseCategory::create([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        session()->flash('success', 'تم إضافة التصنيف بنجاح');

        return redirect()->route('expenses.categories.index');
    }

    public function update(Request $request, $id)
    {
        $category = ExpenseCategory::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update($validated);

        session()->flash('success', 'تم تحديث التصنيف بنجاح');

        return redirect()->route('expenses.categories.index');
    }

    public function destroy($id)
    {
        $category = ExpenseCategory::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        if ($category->expenses()->count() > 0) {
            session()->flash('error', 'لا يمكن حذف التصنيف لوجود مصروفات مرتبطة به');
            return redirect()->route('expenses.categories.index');
        }

        $category->delete();

        session()->flash('success', 'تم حذف التصنيف بنجاح');

        return redirect()->route('expenses.categories.index');
    }
}
