@extends('layouts.app')

@section('title', 'تعديل الطلب #' . $order->order_number)

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-edit text-gold me-2"></i>تعديل الطلب <span class="order-number">#{{ $order->order_number }}</span></h1>
        </div>
        <div class="page-actions">
            <a href="{{ route('sales.orders.show', $order) }}" class="btn btn-outline-gold">
                <i class="fas fa-arrow-right me-1"></i>عودة
            </a>
        </div>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('sales.orders.update', $order) }}">
    @csrf
    @method('PUT')

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
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="content-card">
                <div class="card-header-custom">
                    <h5><i class="fas fa-sliders-h me-1"></i>تحديث الحالة</h5>
                </div>
                <div class="card-body-custom">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">حالة الطلب</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>قيد التحضير</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">حالة الدفع</label>
                        <select name="payment_status" class="form-select">
                            <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                            <option value="partial" {{ $order->payment_status == 'partial' ? 'selected' : '' }}>مدفوع جزئياً</option>
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>مدفوع</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="ملاحظات الطلب">{{ old('notes', $order->notes) }}</textarea>
                    </div>
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

        <div class="col-12">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('sales.orders.show', $order) }}" class="btn btn-outline-gold">
                    <i class="fas fa-times me-1"></i>إلغاء
                </a>
                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-save me-1"></i>حفظ التغييرات
                </button>
            </div>
        </div>
    </div>
</form>
@endsection