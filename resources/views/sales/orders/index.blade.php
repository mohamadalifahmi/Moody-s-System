@extends('layouts.app')

@section('title', 'الطلبات')

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-list text-gold me-2"></i>الطلبات</h1>
        </div>
        <div class="page-actions">
            <a href="{{ route('sales.orders.create') }}" class="btn btn-gold">
                <i class="fas fa-plus-circle me-1"></i>طلب جديد
            </a>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-body-custom">
        <form method="GET" action="{{ route('sales.orders.index') }}" class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="بحث برقم الطلب" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">{{ __('الكل') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="من تاريخ">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="إلى تاريخ">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-gold w-100"><i class="fas fa-filter me-1"></i>تصفية</button>
            </div>
        </form>

        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table dashboard-table">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>الحالة</th>
                            <th>المبلغ</th>
                            <th>الفترة</th>
                            <th>تاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td><span class="order-number">#{{ $order->order_number }}</span></td>
                                <td>
                                    @php
                                        $statusClasses = ['pending' => 'status-pending', 'processing' => 'status-processing', 'confirmed' => 'status-confirmed', 'completed' => 'status-completed', 'cancelled' => 'status-cancelled'];
                                        $class = $statusClasses[$order->status] ?? 'status-pending';
                                    @endphp
                                    <span class="status-badge {{ $class }}">
                                        @if($order->status == 'pending')<i class="fas fa-clock"></i>@endif
                                        @if($order->status == 'processing')<i class="fas fa-spinner"></i>@endif
                                        @if($order->status == 'confirmed')<i class="fas fa-check-circle"></i>@endif
                                        @if($order->status == 'completed')<i class="fas fa-check-double"></i>@endif
                                        @if($order->status == 'cancelled')<i class="fas fa-times-circle"></i>@endif
                                        {{ __('statuses.' . $order->status) }}
                                    </span>
                                </td>
                                <td><span class="amount-cell">{{ CurrencyHelper::formatDual($order->total, $exchangeRate) }}</span></td>
                                <td>{{ $order->session ? '#' . $order->session->session_number : '-' }}</td>
                                <td><span class="time-cell">{{ $order->created_at->format('Y-m-d H:i') }}</span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('sales.orders.show', $order) }}" class="btn-action" title="عرض"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('sales.orders.print', $order) }}" class="btn-action" title="طباعة" target="_blank"><i class="fas fa-print"></i></a>
                                        <form method="POST" action="{{ route('sales.orders.destroy', $order) }}" data-confirm="هل أنت متأكد من حذف الطلب #{{ $order->order_number }}؟" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action" title="حذف"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>لا توجد طلبات</p>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    {{ $orders->links() }}
</div>
@endsection
