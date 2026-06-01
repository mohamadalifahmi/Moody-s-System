@extends('layouts.app')

@section('title', 'تقرير الأرباح والخسائر')

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1>تقرير الأرباح والخسائر</h1>
            <p>من {{ $fromDate }} إلى {{ $toDate }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('reports.profit-loss') }}" class="btn btn-teal">
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
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-circle-plus text-success me-1"></i> الإيرادات</h5>
            </div>
            <div class="card-body-custom">
                <div class="summary-items">
                    <div class="summary-item">
                        <span class="summary-label"><i class="fas fa-money-bill-wave text-success"></i> إجمالي المبيعات</span>
                        <span class="summary-value text-success">{{ CurrencyHelper::formatDual($totalSales, $exchangeRate) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card mt-4">
            <div class="card-header-custom">
                <h5><i class="fas fa-calculator text-gold me-1"></i> ملخص</h5>
            </div>
            <div class="card-body-custom">
                <div class="summary-items">
                    <div class="summary-item">
                        <span class="summary-label"><i class="fas fa-circle-up text-success"></i> إجمالي الإيرادات</span>
                        <span class="summary-value text-success">{{ CurrencyHelper::formatDual($totalSales, $exchangeRate) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label"><i class="fas fa-circle-down text-danger"></i> إجمالي المصروفات</span>
                        <span class="summary-value text-danger">{{ CurrencyHelper::formatDual($totalExpenses, $exchangeRate) }}</span>
                    </div>
                    <div class="summary-item" style="background: {{ $netProfit >= 0 ? 'rgba(52, 211, 153, 0.12)' : 'rgba(248, 113, 113, 0.12)' }}; border-radius: var(--radius-sm);">
                        <span class="summary-label fw-bold"><i class="fas {{ $netProfit >= 0 ? 'fa-circle-check text-success' : 'fa-circle-exclamation text-danger' }}"></i> صافي {{ $netProfit >= 0 ? 'الربح' : 'الخسارة' }}</span>
                        <span class="summary-value fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 18px;">{{ CurrencyHelper::formatDual(abs($netProfit), $exchangeRate) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="content-card h-100">
            <div class="card-header-custom">
                <h5><i class="fas fa-circle-minus text-danger me-1"></i> المصروفات حسب التصنيف</h5>
            </div>
            <div class="card-body-custom p-0">
                @if(count($expensesByCategory) > 0)
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th>التصنيف</th>
                                    <th>المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expensesByCategory as $category)
                                    <tr>
                                        <td><i class="fas fa-tag text-muted me-1"></i> {{ $category->name }}</td>
                                        <td class="amount-cell">{{ CurrencyHelper::formatDual($category->total, $exchangeRate) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td>الإجمالي</td>
                                    <td class="amount-cell text-danger">{{ CurrencyHelper::formatDual($totalExpenses, $exchangeRate) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-receipt"></i>
                        <p>لا توجد مصروفات في هذه الفترة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
