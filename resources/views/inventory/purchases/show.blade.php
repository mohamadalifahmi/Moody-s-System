@extends('layouts.app')

@section('title', 'فاتورة مشتريات')

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-file-invoice text-gold me-2"></i> فاتورة مشتريات #{{ $purchase->invoice_no ?? $purchase->id }}</h1>
            <p class="text-muted">تفاصيل فاتورة المشتريات</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('inventory.purchases.index') }}" class="btn btn-outline-gold"><i class="fas fa-arrow-right"></i> العودة للمشتريات</a>
            <a href="{{ route('inventory.purchases.edit', $purchase->id) }}" class="btn btn-gold"><i class="fas fa-edit"></i> تعديل</a>
        </div>
    </div>
</div>

<div class="content-card mb-4">
    <div class="card-body-custom">
        <div class="row g-4">
            <div class="col-md-4">
                <small class="text-muted d-block">المورد</small>
                <span class="fw-bold">{{ $purchase->supplier->name ?? '—' }}</span>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">رقم الفاتورة</small>
                <span class="fw-bold">{{ $purchase->invoice_no ?? '—' }}</span>
            </div>
            <div class="col-md-2">
                <small class="text-muted d-block">التاريخ</small>
                <span class="fw-bold">{{ $purchase->date->format('Y-m-d') }}</span>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">الحالة</small>
                @switch($purchase->status)
                    @case('pending') <span class="status-badge status-pending"><i class="fas fa-clock"></i> معلق</span> @break
                    @case('received') <span class="status-badge status-processing"><i class="fas fa-check-circle"></i> مستلمة</span> @break
                    @case('paid') <span class="status-badge status-completed"><i class="fas fa-check-double"></i> مدفوعة</span> @break
                    @case('cancelled') <span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> ملغية</span> @break
                    @default <span class="status-badge status-pending">{{ $purchase->status }}</span>
                @endswitch
            </div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="fas fa-box me-1"></i> المنتجات</h5>
    </div>
    <div class="card-body-custom p-0">
        <div class="table-responsive">
            <table class="table dashboard-table mb-0">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>الكمية</th>
                        <th>سعر الوحدة</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? '—' }}</td>
                            <td>{{ number_format($item->quantity, 2) }}</td>
                            <td class="amount-cell">{{ CurrencyHelper::formatDual($item->unit_price, $exchangeRate) }}</td>
                            <td class="amount-cell">{{ CurrencyHelper::formatDual($item->total, $exchangeRate) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-body-custom border-top">
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="d-flex justify-content-between mb-2"><span>الإجمالي:</span><span class="amount-cell fw-bold">{{ CurrencyHelper::formatDual($purchase->total, $exchangeRate) }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span>المدفوع:</span><span class="amount-cell text-success">{{ CurrencyHelper::formatDual($purchase->paid, $exchangeRate) }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span>المتبقي:</span><span class="amount-cell {{ $purchase->due > 0 ? 'text-danger' : 'text-success' }}">{{ CurrencyHelper::formatDual($purchase->due, $exchangeRate) }}</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
