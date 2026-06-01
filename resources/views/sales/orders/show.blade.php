@extends('layouts.app')

@section('title', 'تفاصيل الطلب #' . $order->order_number)

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-file-invoice text-gold me-2"></i>تفاصيل الطلب <span class="order-number">#{{ $order->order_number }}</span></h1>
        </div>
        <div class="page-actions">
            <a href="{{ route('sales.orders.print', $order) }}" class="btn btn-gold" target="_blank">
                <i class="fas fa-print me-1"></i>طباعة
            </a>
            @if($order->status == 'pending')
                <a href="{{ route('sales.orders.edit', $order) }}" class="btn btn-outline-gold">
                    <i class="fas fa-edit me-1"></i>تعديل
                </a>
                <form method="POST" action="{{ route('sales.orders.destroy', $order) }}" data-confirm="هل أنت متأكد من حذف الطلب #{{ $order->order_number }}؟" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-teal">
                        <i class="fas fa-trash-alt me-1"></i>حذف
                    </button>
                </form>
            @endif
            <a href="{{ route('sales.orders.index') }}" class="btn btn-outline-gold">
                <i class="fas fa-arrow-right me-1"></i>رجوع
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-info-circle me-1"></i>معلومات الطلب</h5>
            </div>
            <div class="card-body-custom">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="140" class="text-muted">رقم الطلب</th>
                        <td><span class="order-number">#{{ $order->order_number }}</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted">الحالة</th>
                        <td>
                            @php
                                $statusClasses = ['pending' => 'status-pending', 'processing' => 'status-processing', 'confirmed' => 'status-confirmed', 'completed' => 'status-completed', 'cancelled' => 'status-cancelled'];
                            @endphp
                            <span class="status-badge {{ $statusClasses[$order->status] ?? 'status-pending' }}">
                                @if($order->status == 'pending')<i class="fas fa-clock"></i>@endif
                                @if($order->status == 'processing')<i class="fas fa-spinner"></i>@endif
                                @if($order->status == 'confirmed')<i class="fas fa-check-circle"></i>@endif
                                @if($order->status == 'completed')<i class="fas fa-check-double"></i>@endif
                                @if($order->status == 'cancelled')<i class="fas fa-times-circle"></i>@endif
                                {{ __('statuses.' . $order->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">تاريخ</th>
                        <td><span class="time-cell">{{ $order->created_at->format('Y-m-d H:i:s') }}</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted">الفترة</th>
                        <td>{{ $order->session ? '#' . $order->session->session_number : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">أمين الصندوق</th>
                        <td>{{ $order->user->name ?? '-' }}</td>
                    </tr>
                    @if($order->customer_name)
                    <tr>
                        <th class="text-muted">العميل</th>
                        <td>{{ $order->customer_name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th class="text-muted">نوع الطلب</th>
                        <td>{{ \App\Domains\Core\Helpers\CurrencyHelper::orderTypeLabel($order->order_type) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-credit-card me-1"></i>المدفوعات</h5>
            </div>
            <div class="card-body-custom">
                @if($order->payments && $order->payments->count() > 0)
                    <table class="table dashboard-table mb-0">
                        <thead>
                            <tr>
                                <th>طريقة الدفع</th>
                                <th>المبلغ</th>
                                <th>الوقت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->payments as $payment)
                                <tr>
                                    <td>
                                        @if($payment->method == 'cash')<i class="fas fa-money-bill-wave text-success me-1"></i>نقداً
                                        @elseif($payment->method == 'card')<i class="fas fa-credit-card text-primary me-1"></i>بطاقة
                                        @else<i class="fas fa-ellipsis-h me-1"></i>{{ $payment->method }}
                                        @endif
                                    </td>
                                    <td><span class="amount-cell">{{ CurrencyHelper::formatDual($payment->amount, $exchangeRate) }}</span></td>
                                    <td><span class="time-cell">{{ $payment->created_at->format('Y-m-d H:i') }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-credit-card"></i>
                        <p>لا توجد مدفوعات</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-box me-1"></i>المنتجات</h5>
            </div>
            <div class="card-body-custom">
                <table class="table dashboard-table mb-0">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td><span class="amount-cell">{{ CurrencyHelper::formatDual($item->unit_price, $exchangeRate) }}</span></td>
                                <td><span class="amount-cell">{{ CurrencyHelper::formatDual($item->quantity * $item->unit_price, $exchangeRate) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4 ms-auto">
        <div class="content-card">
            <div class="card-body-custom">
                <div class="summary-items">
                    <div class="summary-item">
                        <span class="summary-label"><i class="fas fa-shopping-cart text-muted"></i>المجموع الفرعي</span>
                        <span class="summary-value">{{ CurrencyHelper::formatDual($order->subtotal, $exchangeRate) }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="summary-item">
                        <span class="summary-label"><i class="fas fa-tag text-muted"></i>الخصم</span>
                        <span class="summary-value">-{{ CurrencyHelper::formatDual($order->discount, $exchangeRate) }}</span>
                    </div>
                    @endif
                    <div class="summary-item">
                        <span class="summary-label"><i class="fas fa-percent text-muted"></i>الضريبة</span>
                        <span class="summary-value">{{ CurrencyHelper::formatDual($order->tax, $exchangeRate) }}</span>
                    </div>
                    <div class="summary-item" style="background: rgba(201,169,97,0.1); border: 1px solid var(--gold);">
                        <span class="summary-label" style="font-weight:800;"><i class="fas fa-receipt text-gold"></i>الإجمالي</span>
                        <span class="summary-value" style="font-size:20px;color:var(--gold-dark);">{{ CurrencyHelper::formatDual($order->total, $exchangeRate) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
