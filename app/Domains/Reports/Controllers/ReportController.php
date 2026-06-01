<?php

namespace App\Domains\Reports\Controllers;

use App\Domains\Auth\Models\Tenant;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Sales\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index()
    {
        return view('reports.index');
    }

    public function daily(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));

        $tenantId = Auth::user()->tenant_id;

        $orders = Order::where('tenant_id', $tenantId)
            ->whereDate('created_at', $date)
            ->whereIn('status', ['completed', 'paid'])
            ->get();

        $totalSales = $orders->sum('total');
        $orderCount = $orders->count();

        $expenses = Expense::where('tenant_id', $tenantId)
            ->whereDate('date', $date)
            ->get();

        $totalExpenses = $expenses->sum('amount');
        $expensesCount = $expenses->count();

        $netProfit = $totalSales - $totalExpenses;

        $previousDate = now()->createFromFormat('Y-m-d', $date)->subDay()->format('Y-m-d');
        $nextDate = now()->createFromFormat('Y-m-d', $date)->addDay()->format('Y-m-d');

        if ($request->wantsJson()) {
            return response()->json([
                'date' => $date,
                'total_sales' => $totalSales,
                'sales_count' => $orderCount,
                'total_expenses' => $totalExpenses,
                'expenses_count' => $expensesCount,
                'profit' => $netProfit,
            ]);
        }

        $exchangeRate = Tenant::find(Auth::user()->tenant_id)->settings['exchange_rate'] ?? 89500;

        return view('reports.daily', compact(
            'date', 'orders', 'expenses', 'totalSales', 'orderCount',
            'totalExpenses', 'expensesCount', 'netProfit',
            'previousDate', 'nextDate', 'exchangeRate'
        ));
    }

    public function monthly(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $tenantId = Auth::user()->tenant_id;

        $startDate = $month . '-01';
        $endDate = now()->createFromFormat('Y-m-d', $startDate)->endOfMonth()->format('Y-m-d');

        $year = now()->createFromFormat('Y-m', $month)->format('Y');

        $prev = now()->createFromFormat('Y-m-d', $startDate)->subMonth();
        $next = now()->createFromFormat('Y-m-d', $startDate)->addMonth();
        $previousMonth = $prev->format('Y-m');
        $previousYear = $prev->format('Y');
        $nextMonth = $next->format('Y-m');
        $nextYear = $next->format('Y');

        $dailyBreakdown = [];

        $current = now()->createFromFormat('Y-m-d', $startDate);
        $end = now()->createFromFormat('Y-m-d', $endDate);

        while ($current <= $end) {
            $day = $current->format('Y-m-d');

            $daySales = Order::where('tenant_id', $tenantId)
                ->whereDate('created_at', $day)
                ->whereIn('status', ['completed', 'paid'])
                ->sum('total');

            $dayExpenses = Expense::where('tenant_id', $tenantId)
                ->whereDate('date', $day)
                ->sum('amount');

            $dailyBreakdown[] = [
                'date' => $day,
                'sales' => $daySales,
                'expenses' => $dayExpenses,
                'profit' => $daySales - $dayExpenses,
            ];

            $current->addDay();
        }

        $totalSales = array_sum(array_column($dailyBreakdown, 'sales'));
        $totalExpenses = array_sum(array_column($dailyBreakdown, 'expenses'));
        $netProfit = $totalSales - $totalExpenses;
        $orderCount = Order::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereIn('status', ['completed', 'paid'])
            ->count();

        if ($request->wantsJson()) {
            return response()->json([
                'month' => $month,
                'daily_data' => $dailyBreakdown,
                'total_sales' => $totalSales,
                'total_expenses' => $totalExpenses,
                'total_profit' => $netProfit,
            ]);
        }

        $exchangeRate = Tenant::find(Auth::user()->tenant_id)->settings['exchange_rate'] ?? 89500;

        return view('reports.monthly', compact(
            'month', 'year', 'dailyBreakdown', 'totalSales',
            'totalExpenses', 'netProfit', 'orderCount',
            'previousMonth', 'previousYear', 'nextMonth', 'nextYear',
            'exchangeRate'
        ));
    }

    public function profitLoss(Request $request, $fromDate = null, $toDate = null)
    {
        $fromDate = $fromDate ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $toDate ?? now()->endOfMonth()->format('Y-m-d');

        $start = \Carbon\Carbon::parse($fromDate);
        $end = \Carbon\Carbon::parse($toDate);
        $daysDiff = $start->diffInDays($end) + 1;
        $previousFrom = $start->copy()->subDays($daysDiff)->format('Y-m-d');
        $previousTo = $start->copy()->subDay()->format('Y-m-d');
        $nextFrom = $end->copy()->addDay()->format('Y-m-d');
        $nextTo = $end->copy()->addDays($daysDiff)->format('Y-m-d');

        $tenantId = Auth::user()->tenant_id;

        $totalSales = Order::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->whereIn('status', ['completed', 'paid'])
            ->sum('total');

        $totalExpenses = Expense::where('tenant_id', $tenantId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->sum('amount');

        $netProfit = $totalSales - $totalExpenses;

        $expensesByCategory = Expense::with('category')
            ->where('tenant_id', $tenantId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->selectRaw('expense_category_id, SUM(amount) as total')
            ->groupBy('expense_category_id')
            ->get();

        $exchangeRate = Tenant::find(Auth::user()->tenant_id)->settings['exchange_rate'] ?? 89500;

        return view('reports.profit-loss', compact(
            'fromDate', 'toDate', 'totalSales',
            'totalExpenses', 'netProfit', 'expensesByCategory',
            'exchangeRate', 'previousFrom', 'previousTo', 'nextFrom', 'nextTo'
        ));
    }

    public function exportDaily(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $tenantId = Auth::user()->tenant_id;

        $sales = Order::where('tenant_id', $tenantId)
            ->whereDate('created_at', $date)
            ->whereIn('status', ['completed', 'paid'])
            ->get();

        $expenses = Expense::with('category')
            ->where('tenant_id', $tenantId)
            ->whereDate('date', $date)
            ->get();

        $filename = 'report-' . $date . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($date, $sales, $expenses) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['تقرير يومي - ' . $date]);
            fputcsv($handle, []);

            fputcsv($handle, ['المبيعات']);
            fputcsv($handle, ['رقم الطلب', 'الحالة', 'المبلغ', 'تاريخ الإنشاء']);

            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->order_number,
                    $sale->status,
                    number_format($sale->total, 2),
                    $sale->created_at->format('Y-m-d H:i'),
                ]);
            }

            fputcsv($handle, ['إجمالي المبيعات', number_format($sales->sum('total'), 2)]);
            fputcsv($handle, []);

            fputcsv($handle, ['المصروفات']);
            fputcsv($handle, ['التصنيف', 'الوصف', 'المبلغ', 'طريقة الدفع']);

            foreach ($expenses as $expense) {
                fputcsv($handle, [
                    $expense->category->name ?? '-',
                    $expense->description,
                    number_format($expense->amount, 2),
                    $expense->payment_method,
                ]);
            }

            fputcsv($handle, ['إجمالي المصروفات', number_format($expenses->sum('amount'), 2)]);
            fputcsv($handle, []);
            fputcsv($handle, ['صافي الربح', number_format($sales->sum('total') - $expenses->sum('amount'), 2)]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportProfitLoss(Request $request)
    {
        $fromDate = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to', now()->endOfMonth()->format('Y-m-d'));

        $tenantId = Auth::user()->tenant_id;

        $totalSales = Order::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->whereIn('status', ['completed', 'paid'])
            ->sum('total');

        $expenses = Expense::with('category')
            ->where('tenant_id', $tenantId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->selectRaw('expense_category_id, SUM(amount) as total')
            ->groupBy('expense_category_id')
            ->get();

        $totalExpenses = $expenses->sum('total');
        $netProfit = $totalSales - $totalExpenses;

        $filename = 'profit-loss-' . $fromDate . '-to-' . $toDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($fromDate, $toDate, $totalSales, $expenses, $totalExpenses, $netProfit) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['قائمة الأرباح والخسائر']);
            fputcsv($handle, ['من ' . $fromDate . ' إلى ' . $toDate]);
            fputcsv($handle, []);
            fputcsv($handle, ['البيان', 'المبلغ']);
            fputcsv($handle, ['إجمالي المبيعات', number_format($totalSales, 2)]);

            foreach ($expenses as $expense) {
                fputcsv($handle, ['مصروفات - ' . ($expense->category->name ?? 'عام'), number_format($expense->total, 2)]);
            }

            fputcsv($handle, ['إجمالي المصروفات', number_format($totalExpenses, 2)]);
            fputcsv($handle, ['صافي ' . ($netProfit >= 0 ? 'الربح' : 'الخسارة'), number_format(abs($netProfit), 2)]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
