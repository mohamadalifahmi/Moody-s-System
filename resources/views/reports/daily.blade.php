@extends('layouts.app')

@section('title', 'التقرير اليومي - ' . $date)

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1>التقرير اليومي</h1>
            <p>{{ $date }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('reports.daily', ['date' => $previousDate]) }}" class="btn btn-outline-gold">
                <i class="fas fa-chevron-right"></i> اليوم السابق
            </a>
            <a href="{{ route('reports.daily', ['date' => $nextDate]) }}" class="btn btn-outline-gold">
                اليوم التالي <i class="fas fa-chevron-left"></i>
            </a>
            <a href="{{ route('reports.export-daily', ['date' => $date]) }}" class="btn btn-teal">
                <i class="fas fa-download"></i> تصدير CSV
            </a>
        </div>
    </div>
</div>

<div class="kpi-row">
    <div class="kpi-card kpi-sales">
        <div class="kpi-icon"><i class="fas fa-money-bill-wave"></i></div>
        <div class="kpi-info">
            <span class="kpi-label">إجمالي المبيعات</span>
            <span class="kpi-value">{{ CurrencyHelper::formatDual($totalSales, $exchangeRate) }}</span>
        </div>
    </div>
    <div class="kpi-card kpi-expenses">
        <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
        <div class="kpi-info">
            <span class="kpi-label">إجمالي المصروفات</span>
            <span class="kpi-value">{{ CurrencyHelper::formatDual($totalExpenses, $exchangeRate) }}</span>
        </div>
    </div>
    <div class="kpi-card kpi-stock">
        <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
        <div class="kpi-info">
            <span class="kpi-label">صافي الربح</span>
            <span class="kpi-value">{{ CurrencyHelper::formatDual($netProfit, $exchangeRate) }}</span>
        </div>
    </div>
    <div class="kpi-card kpi-orders">
        <div class="kpi-icon"><i class="fas fa-shopping-cart"></i></div>
        <div class="kpi-info">
            <span class="kpi-label">عدد الطلبات</span>
            <span class="kpi-value">{{ number_format($orderCount, 0) }}</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-money-bill-wave text-gold me-1"></i> المبيعات</h5>
            </div>
            <div class="card-body-custom p-0">
                @if(count($orders) > 0)
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>الحالة</th>
                                    <th>الوقت</th>
                                    <th>المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td><span class="order-number">#{{ $order->order_number }}</span></td>
                                        <td>{{ $order->customer_name ?? 'زبون' }}</td>
                                        <td><span class="status-badge status-{{ $order->status }}">{{ $order->status_label ?? $order->status }}</span></td>
                                        <td class="time-cell">{{ $order->created_at->format('h:i A') }}</td>
                                        <td class="amount-cell">{{ CurrencyHelper::formatDual($order->total, $exchangeRate) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="4" class="text-start">الإجمالي</td>
                                    <td class="amount-cell">{{ CurrencyHelper::formatDual($totalSales, $exchangeRate) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-receipt"></i>
                        <p>لا توجد مبيعات في هذا اليوم</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-receipt text-danger me-1"></i> المصروفات</h5>
            </div>
            <div class="card-body-custom p-0">
                @if(count($expenses) > 0)
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th>البيان</th>
                                    <th>التصنيف</th>
                                    <th>الوقت</th>
                                    <th>المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->description }}</td>
                                        <td>{{ $expense->category->name ?? $expense->category_name ?? 'عام' }}</td>
                                        <td class="time-cell">{{ $expense->created_at->format('h:i A') }}</td>
                                        <td class="amount-cell">{{ CurrencyHelper::formatDual($expense->amount, $exchangeRate) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-start">الإجمالي</td>
                                    <td class="amount-cell">{{ CurrencyHelper::formatDual($totalExpenses, $exchangeRate) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-receipt"></i>
                        <p>لا توجد مصروفات في هذا اليوم</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection