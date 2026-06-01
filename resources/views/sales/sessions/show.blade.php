@extends('layouts.app')

@section('title', 'تفاصيل فترة المبيعات')

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-chair text-gold me-2"></i>تفاصيل فترة المبيعات <span class="fw-bold">#{{ $session->session_number }}</span></h1>
        </div>
        <div class="page-actions">
            <a href="{{ route('sales.sessions.index') }}" class="btn btn-outline-gold">
                <i class="fas fa-arrow-right me-1"></i>رجوع
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-info-circle me-1"></i>معلومات الفترة</h5>
            </div>
            <div class="card-body-custom">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="140" class="text-muted">رقم الفترة</th>
                        <td><span class="fw-bold">#{{ $session->session_number }}</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted">الحالة</th>
                        <td>
                            @if($session->status == 'open')
                                <span class="status-badge status-processing"><i class="fas fa-circle me-1"></i>مفتوحة</span>
                            @else
                                <span class="status-badge status-completed"><i class="fas fa-check-circle me-1"></i>مغلقة</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">وقت الفتح</th>
                        <td><span class="time-cell">{{ $session->opened_at->format('Y-m-d H:i:s') }}</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted">وقت الإغلاق</th>
                        <td><span class="time-cell">{{ $session->closed_at ? $session->closed_at->format('Y-m-d H:i:s') : '-' }}</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted">أمين الصندوق</th>
                        <td>{{ $session->openedBy->name ?? '-' }}</td>
                    </tr>
                    @if($session->opened_at && $session->closed_at)
                    <tr>
                        <th class="text-muted">المدة</th>
                        <td>
                            @php
                                $duration = $session->opened_at->diff($session->closed_at);
                            @endphp
                            {{ $duration->h }} ساعة {{ $duration->i }} دقيقة
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-chart-pie me-1"></i>ملخص المدفوعات</h5>
            </div>
            <div class="card-body-custom">
                <div class="summary-items">
                    <div class="summary-item">
                        <span class="summary-label"><i class="fas fa-money-bill-wave text-success"></i>نقداً</span>
                        <span class="summary-value">{{ CurrencyHelper::formatDual($session->total_cash ?? 0, $exchangeRate) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label"><i class="fas fa-credit-card text-primary"></i>بطاقة</span>
                        <span class="summary-value">{{ CurrencyHelper::formatDual($session->total_card ?? 0, $exchangeRate) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label"><i class="fas fa-ellipsis-h text-muted"></i>أخرى</span>
                        <span class="summary-value">{{ CurrencyHelper::formatDual($session->total_other ?? 0, $exchangeRate) }}</span>
                    </div>
                    <div class="summary-item" style="background: rgba(201,169,97,0.1); border: 1px solid var(--gold);">
                        <span class="summary-label" style="font-weight:800;"><i class="fas fa-receipt text-gold"></i>الإجمالي الكلي</span>
                        <span class="summary-value" style="font-size:20px;color:var(--gold-dark);">{{ CurrencyHelper::formatDual($session->grand_total ?? 0, $exchangeRate) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-list me-1"></i>طلبات الفترة ({{ $session->orders->count() }})</h5>
            </div>
            <div class="card-body-custom">
                @if($session->orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table dashboard-table mb-0">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>الحالة</th>
                                    <th>المبلغ</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($session->orders as $order)
                                    <tr>
                                        <td><span class="order-number">#{{ $order->order_number }}</span></td>
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
                                        <td><span class="amount-cell">{{ CurrencyHelper::formatDual($order->total, $exchangeRate) }}</span></td>
                                        <td><span class="time-cell">{{ $order->created_at->format('Y-m-d H:i') }}</span></td>
                                        <td>
                                            <a href="{{ route('sales.orders.show', $order) }}" class="btn-action" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>لا توجد طلبات في هذه الفترة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
