@extends('layouts.app')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp

@section('title', 'فاتورة #' . $invoice->invoice_number)

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-file-invoice text-gold me-2"></i> فاتورة #{{ $invoice->invoice_number }}</h1>
        </div>
        <div class="page-actions">
            <a href="{{ route('invoicing.invoices.index') }}" class="btn btn-outline-gold"><i class="fas fa-arrow-right"></i> العودة</a>
            <a href="{{ route('invoicing.invoices.print', $invoice->id) }}" class="btn btn-outline-gold" target="_blank"><i class="fas fa-print"></i> طباعة</a>
            @if(in_array($invoice->status, ['draft', 'issued']))
                <a href="{{ route('invoicing.invoices.edit', $invoice->id) }}" class="btn btn-gold"><i class="fas fa-edit"></i> تعديل</a>
            @endif
            @if($invoice->status === 'issued')
                <form method="POST" action="{{ route('invoicing.invoices.mark-paid', $invoice->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-teal" onclick="return confirm('تأكيد تحديد الفاتورة كمدفوعة؟')"><i class="fas fa-check-circle"></i> علام مدفوعة</button>
                </form>
            @endif
            @if(in_array($invoice->status, ['draft', 'issued']))
                <form method="POST" action="{{ route('invoicing.invoices.mark-cancelled', $invoice->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('تأكيد إلغاء الفاتورة؟')"><i class="fas fa-ban"></i> إلغاء</button>
                </form>
            @endif
        </div>
    </div>
</div>

<div class="content-card mb-4">
    <div class="card-body-custom">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="brand-icon" style="width:60px;height:60px;"><svg viewBox="0 0 40 40" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="sg2" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#232334"/><stop offset="0.6" stop-color="#14141f"/><stop offset="1" stop-color="#0a0a11"/></linearGradient><linearGradient id="sg22" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f7df9b"/><stop offset="0.4" stop-color="#e8bd4f"/><stop offset="0.8" stop-color="#c9962b"/><stop offset="1" stop-color="#9a6c12"/></linearGradient></defs><rect x="0" y="0" width="40" height="40" rx="9.2" fill="url(#sg2)"/><rect x="1.3" y="1.3" width="37.4" height="37.4" rx="7.8" fill="none" stroke="#e8bd4f" stroke-opacity="0.35" stroke-width="0.7"/><path d="M7.9 29.7 L7.9 14.9 L13.5 14.9 L20 24.5 L26.5 14.9 L32.2 14.9 L32.2 29.7 L26.6 29.7 L26.6 22.8 L20 28.8 L13.4 22.8 L13.4 29.7 Z" fill="url(#sg22)"/></svg></div>
                    <div>
                        <h3 class="mb-0 fw-bold">{{ config('app.name') }}</h3>
                        <p class="text-muted mb-0">{{ config('app.address') ?? config('app.name') }}</p>
                        <p class="text-muted mb-0">{{ config('app.phone') ?? '—' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-start">
                <h2 class="mb-1 order-number">{{ $invoice->invoice_number }}</h2>
                <p class="text-muted mb-1">بتاريخ: {{ $invoice->created_at->format('Y-m-d') }}</p>
                @switch($invoice->status)
                    @case('draft') <span class="status-badge status-pending"><i class="fas fa-file"></i> مسودة</span> @break
                    @case('issued') <span class="status-badge status-processing"><i class="fas fa-check-circle"></i> صادرة</span> @break
                    @case('paid') <span class="status-badge status-completed"><i class="fas fa-check-double"></i> مدفوعة</span> @break
                    @case('cancelled') <span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> ملغية</span> @break
                @endswitch
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-1">معلومات العميل</h6>
                <p class="mb-0 fw-bold">{{ $invoice->customer_name }}</p>
                @if($invoice->customer_phone)<p class="mb-0 text-muted" dir="ltr">{{ $invoice->customer_phone }}</p>@endif
            </div>
            @if($invoice->order)
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">الطلب المرتبط</h6>
                    <p class="mb-0 fw-bold">طلب #{{ $invoice->order->id }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="content-card mb-4">
    <div class="card-header-custom">
        <h5><i class="fas fa-box me-1"></i> المنتجات</h5>
    </div>
    <div class="card-body-custom p-0">
        <div class="table-responsive">
            <table class="table dashboard-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المنتج</th>
                        <th>الكمية</th>
                        <th>سعر الوحدة</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $index => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->name }}</td>
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
            <div class="col-md-5">
                <div class="d-flex justify-content-between mb-2"><span>الإجمالي الفرعي:</span><span class="amount-cell">{{ CurrencyHelper::formatDual($invoice->subtotal, $exchangeRate) }}</span></div>
                @if($invoice->discount > 0)
                    <div class="d-flex justify-content-between mb-2"><span>الخصم:</span><span class="amount-cell text-danger">-{{ CurrencyHelper::formatDual($invoice->discount, $exchangeRate) }}</span></div>
                @endif
                @if($invoice->tax > 0)
                    <div class="d-flex justify-content-between mb-2"><span>الضريبة:</span><span class="amount-cell">{{ CurrencyHelper::formatDual($invoice->tax, $exchangeRate) }}</span></div>
                @endif
                <hr>
                <div class="d-flex justify-content-between mb-2 fw-bold"><span>الإجمالي:</span><span class="amount-cell text-gold fs-5">{{ CurrencyHelper::formatDual($invoice->total, $exchangeRate) }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span>المدفوع:</span><span class="amount-cell text-success">{{ CurrencyHelper::formatDual($invoice->paid, $exchangeRate) }}</span></div>
                <div class="d-flex justify-content-between mb-2 fw-bold"><span>المتبقي:</span><span class="amount-cell {{ $invoice->due > 0 ? 'text-danger' : 'text-success' }}">{{ CurrencyHelper::formatDual($invoice->due, $exchangeRate) }}</span></div>
            </div>
        </div>
    </div>
</div>

@if($invoice->payments->count() > 0)
    <div class="content-card">
        <div class="card-header-custom">
            <h5><i class="fas fa-credit-card me-1"></i> سجل المدفوعات</h5>
        </div>
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table dashboard-table mb-0">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>المبلغ</th>
                            <th>طريقة الدفع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                            <tr>
                                <td class="time-cell">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                <td class="amount-cell">{{ CurrencyHelper::formatDual($payment->amount, $exchangeRate) }}</td>
                                <td>{{ $payment->payment_method ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@if($invoice->notes)
    <div class="content-card mt-4">
        <div class="card-body-custom">
            <h6 class="text-muted mb-2"><i class="fas fa-sticky-note me-1"></i> ملاحظات</h6>
            <p class="mb-0">{{ $invoice->notes }}</p>
        </div>
    </div>
@endif
@endsection
