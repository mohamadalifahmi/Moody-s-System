@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-chart-pie me-2"></i>لوحة التحكم</h1>
            <p class="text-muted">مرحباً بعودتك! إليك ملخص اليوم</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('sales.orders.create') }}" class="btn btn-gold"><i class="fas fa-plus-circle me-1"></i> طلب جديد</a>
            <a href="{{ route('expenses.create') }}" class="btn btn-outline-gold"><i class="fas fa-plus-circle me-1"></i> إضافة مصروف</a>
            <form method="POST" action="{{ route('sales.sessions.store') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-teal"><i class="fas fa-chair me-1"></i> فتح فترة مبيعات</button>
            </form>
        </div>
    </div>
</div>

<div class="kpi-row">
    <div class="kpi-card kpi-sales">
        <div class="kpi-icon">
            <i class="fas fa-coins"></i>
        </div>
        <div class="kpi-info">
            <span class="kpi-label">مبيعات اليوم</span>
            <span class="kpi-value">{{ CurrencyHelper::formatDual($todaySalesTotal ?? 0, $exchangeRate) }}</span>
            <span class="kpi-change positive">
                <i class="fas fa-arrow-up"></i> {{ $todaySalesCount ?? 0 }} طلب
            </span>
        </div>
    </div>
    <div class="kpi-card kpi-expenses">
        <div class="kpi-icon">
            <i class="fas fa-receipt"></i>
        </div>
        <div class="kpi-info">
            <span class="kpi-label">المصروفات اليوم</span>
            <span class="kpi-value">{{ CurrencyHelper::formatDual($todayExpensesTotal ?? 0, $exchangeRate) }}</span>
            <span class="kpi-change negative">
                <i class="fas fa-arrow-down"></i> مصروفات
            </span>
        </div>
    </div>
    <div class="kpi-card kpi-orders">
        <div class="kpi-icon">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="kpi-info">
            <span class="kpi-label">الطلبات النشطة</span>
            <span class="kpi-value">{{ $activeOrdersCount ?? 0 }}</span>
            <span class="kpi-change">
                <i class="fas fa-clock"></i> قيد التنفيذ
            </span>
        </div>
    </div>
    <div class="kpi-card kpi-stock">
        <div class="kpi-icon">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="kpi-info">
            <span class="kpi-label">منتجات منخفضة</span>
            <span class="kpi-value">{{ $lowStockProductsCount ?? 0 }}</span>
            <span class="kpi-change negative">
                <i class="fas fa-exclamation-triangle"></i> تحتاج إعادة طلب
            </span>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-history me-2"></i>آخر الطلبات</h5>
                <a href="{{ route('sales.orders.index') }}" class="btn btn-sm btn-outline-gold">عرض الكل <i class="fas fa-arrow-left me-1"></i></a>
            </div>
            <div class="card-body-custom">
                @if(isset($recentOrders) && !$recentOrders->isEmpty())
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>رقم الطلب</th>
                                    <th>الحالة</th>
                                    <th>المبلغ</th>
                                    <th>الوقت</th>
                                    <th>خيارات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="order-number">#{{ $order->order_number ?? $order->id }}</span></td>
                                        <td>
                                            @php
                                                $status = $order->status ?? 'pending';
                                                $statusClasses = [
                                                    'pending' => 'status-pending',
                                                    'completed' => 'status-completed',
                                                    'cancelled' => 'status-cancelled',
                                                    'processing' => 'status-processing',
                                                    'confirmed' => 'status-confirmed',
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'قيد الانتظار',
                                                    'completed' => 'مكتمل',
                                                    'cancelled' => 'ملغي',
                                                    'processing' => 'قيد التحضير',
                                                    'confirmed' => 'مؤكد',
                                                ];
                                            @endphp
                                            <span class="status-badge {{ $statusClasses[$status] ?? 'status-pending' }}">
                                                {{ $statusLabels[$status] ?? $status }}
                                            </span>
                                        </td>
                                        <td class="amount-cell">{{ CurrencyHelper::formatDual($order->total ?? $order->amount ?? 0, $exchangeRate) }}</td>
                                        <td class="time-cell">{{ $order->created_at ? $order->created_at->format('h:i A') : '--' }}</td>
                                        <td>
                                            <a href="{{ route('sales.orders.show', $order) }}" class="btn btn-sm btn-action" title="عرض"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('sales.orders.edit', $order) }}" class="btn btn-sm btn-action" title="تعديل"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>لا توجد طلبات حديثة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-bolt me-2"></i>إجراءات سريعة</h5>
            </div>
            <div class="card-body-custom">
                <div class="quick-actions">
                    <a href="{{ route('sales.orders.create') }}" class="quick-action-btn">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <i class="fas fa-cart-plus"></i>
                        </div>
                        <span>طلب جديد</span>
                    </a>
                    <a href="{{ route('expenses.create') }}" class="quick-action-btn">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <span>إضافة مصروف</span>
                    </a>
                    <form method="POST" action="{{ route('sales.sessions.store') }}" style="display:contents;">
                        @csrf
                        <button type="submit" class="quick-action-btn" style="border:none;width:100%;text-align:center;">
                            <div class="qa-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                                <i class="fas fa-chair"></i>
                            </div>
                            <span>فترة مبيعات</span>
                        </button>
                    </form>
                    <a href="{{ route('inventory.products.create') }}" class="quick-action-btn">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                            <i class="fas fa-box"></i>
                        </div>
                        <span>إضافة منتج</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-chart-simple me-2"></i>ملخص سريع</h5>
            </div>
            <div class="card-body-custom">
                <div class="summary-items">
                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="fas fa-shopping-cart text-gold"></i>
                            <span>إجمالي المبيعات (شهري)</span>
                        </div>
                        <span class="summary-value">{{ CurrencyHelper::formatDual($monthlySalesTotal ?? 0, $exchangeRate) }}</span>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="fas fa-users text-teal"></i>
                            <span>إجمالي العملاء</span>
                        </div>
                        <span class="summary-value">{{ $totalCustomers ?? 0 }}</span>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="fas fa-utensils text-primary"></i>
                            <span>عدد المنتجات</span>
                        </div>
                        <span class="summary-value">{{ $totalProducts ?? 0 }}</span>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="fas fa-truck text-success"></i>
                            <span>الموردين</span>
                        </div>
                        <span class="summary-value">{{ $totalSuppliers ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection