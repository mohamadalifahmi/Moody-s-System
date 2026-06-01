@extends('layouts.app')

@section('title', 'التقرير الشهري - ' . $month . ' ' . $year)

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1>التقرير الشهري</h1>
            <p>{{ $month }} {{ $year }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('reports.monthly', ['month' => $previousMonth, 'year' => $previousYear]) }}" class="btn btn-outline-gold">
                <i class="fas fa-chevron-right"></i> الشهر السابق
            </a>
            <a href="{{ route('reports.monthly', ['month' => $nextMonth, 'year' => $nextYear]) }}" class="btn btn-outline-gold">
                الشهر التالي <i class="fas fa-chevron-left"></i>
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

<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="fas fa-table text-gold me-1"></i> تفاصيل الأيام</h5>
    </div>
    <div class="card-body-custom p-0">
        @if(count($dailyBreakdown) > 0)
            <div class="table-responsive">
                <table class="table dashboard-table">
                    <thead>
                        <tr>
                            <th>اليوم</th>
                            <th>المبيعات</th>
                            <th>المصروفات</th>
                            <th>صافي الربح</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyBreakdown as $day)
                            <tr>
                                <td>{{ $day['date'] }}</td>
                                <td class="amount-cell">{{ CurrencyHelper::formatDual($day['sales'], $exchangeRate) }}</td>
                                <td class="amount-cell">{{ CurrencyHelper::formatDual($day['expenses'], $exchangeRate) }}</td>
                                <td class="amount-cell {{ ($day['profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">{{ CurrencyHelper::formatDual($day['profit'] ?? 0, $exchangeRate) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>الإجمالي</td>
                            <td class="amount-cell">{{ CurrencyHelper::formatDual($totalSales, $exchangeRate) }}</td>
                            <td class="amount-cell">{{ CurrencyHelper::formatDual($totalExpenses, $exchangeRate) }}</td>
                            <td class="amount-cell {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ CurrencyHelper::formatDual($netProfit, $exchangeRate) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-calendar-alt"></i>
                <p>لا توجد بيانات لهذا الشهر</p>
            </div>
        @endif
    </div>
</div>
@endsection