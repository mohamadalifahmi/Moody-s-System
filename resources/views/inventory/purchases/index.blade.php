@extends('layouts.app')

@section('title', 'المشتريات')

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-truck-loading text-gold me-2"></i> المشتريات</h1>
            <p class="text-muted">إدارة فواتير المشتريات</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('inventory.purchases.create') }}" class="btn btn-gold"><i class="fas fa-plus-circle"></i> إضافة مشتريات جديدة</a>
        </div>
    </div>
</div>

<div class="content-card mb-4">
    <div class="card-body-custom">
        <form method="GET" action="{{ route('inventory.purchases.index') }}" class="row g-3">
            <div class="col-md-3">
                <select name="supplier_id" class="form-select">
                    <option value="">جميع الموردين</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>مستلمة</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" placeholder="من تاريخ" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" placeholder="إلى تاريخ" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-teal"><i class="fas fa-filter"></i> تصفية</button>
                <a href="{{ route('inventory.purchases.index') }}" class="btn btn-outline-gold"><i class="fas fa-undo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="content-card">
    <div class="card-body-custom p-0">
        @if($purchases->count() > 0)
            <div class="table-responsive">
                <table class="table dashboard-table mb-0">
                    <thead>
                        <tr>
                            <th>رقم الفاتورة</th>
                            <th>المورد</th>
                            <th>التاريخ</th>
                            <th>الإجمالي</th>
                            <th>المدفوع</th>
                            <th>المتبقي</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases as $purchase)
                            <tr>
                                <td><span class="order-number">{{ $purchase->invoice_no ?? '#' . $purchase->id }}</span></td>
                                <td>{{ $purchase->supplier->name ?? '—' }}</td>
                                <td class="time-cell">{{ $purchase->date->format('Y-m-d') }}</td>
<td class="amount-cell">{{ CurrencyHelper::formatDual($purchase->total, $exchangeRate) }}</td>
<td class="amount-cell">{{ CurrencyHelper::formatDual($purchase->paid, $exchangeRate) }}</td>
<td class="amount-cell {{ $purchase->due > 0 ? 'text-danger' : 'text-success' }}">{{ CurrencyHelper::formatDual($purchase->due, $exchangeRate) }}</td>
                                <td>
                                    @switch($purchase->status)
                                        @case('pending')
                                            <span class="status-badge status-pending"><i class="fas fa-clock"></i> معلق</span>
                                            @break
                                        @case('received')
                                            <span class="status-badge status-processing"><i class="fas fa-check-circle"></i> مستلمة</span>
                                            @break
                                        @case('paid')
                                            <span class="status-badge status-completed"><i class="fas fa-check-double"></i> مدفوعة</span>
                                            @break
                                        @case('cancelled')
                                            <span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> ملغية</span>
                                            @break
                                        @default
                                            <span class="status-badge status-pending">{{ $purchase->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('inventory.purchases.show', $purchase->id) }}" class="btn-action" title="عرض"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('inventory.purchases.edit', $purchase->id) }}" class="btn-action" title="تعديل"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{ route('inventory.purchases.destroy', $purchase->id) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف فاتورة المشتريات هذه؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action" title="حذف"><i class="fas fa-trash text-danger"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body-custom border-top">{{ $purchases->links() }}</div>
        @else
            <div class="empty-state">
                <i class="fas fa-truck-loading"></i>
                <p>لا توجد مشتريات بعد</p>
                <a href="{{ route('inventory.purchases.create') }}" class="btn btn-gold mt-3"><i class="fas fa-plus-circle"></i> إضافة مشتريات جديدة</a>
            </div>
        @endif
    </div>
</div>
@endsection
