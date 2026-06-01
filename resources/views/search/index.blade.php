@extends('layouts.app')

@section('title', 'نتائج البحث: ' . $q)

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-search text-gold me-2"></i> نتائج البحث</h1>
            <p class="text-muted">نتائج البحث عن: "{{ $q }}"</p>
        </div>
    </div>
</div>

@php $hasResults = $products->count() > 0 || $suppliers->count() > 0 || $orders->count() > 0 || $expenses->count() > 0; @endphp

@if($hasResults)
    @if($products->count() > 0)
        <div class="content-card mb-4">
            <div class="card-header-custom">
                <h5><i class="fas fa-box text-gold me-1"></i> المنتجات</h5>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table dashboard-table mb-0">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>SKU</th>
                                <th>سعر البيع</th>
                                <th>الرصيد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td><a href="{{ route('inventory.products.edit', $product->id) }}" class="text-gold">{{ $product->name }}</a></td>
                                    <td>{{ $product->sku ?? '—' }}</td>
                                    <td class="amount-cell">{{ CurrencyHelper::formatDual($product->sale_price, $exchangeRate) }}</td>
                                    <td>{{ $product->stock_quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($suppliers->count() > 0)
        <div class="content-card mb-4">
            <div class="card-header-custom">
                <h5><i class="fas fa-handshake text-gold me-1"></i> الموردين</h5>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table dashboard-table mb-0">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>البريد</th>
                                <th>الهاتف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <td><a href="{{ route('inventory.suppliers.index') }}" class="text-gold">{{ $supplier->name }}</a></td>
                                    <td>{{ $supplier->email ?? '—' }}</td>
                                    <td>{{ $supplier->phone ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($orders->count() > 0)
        <div class="content-card mb-4">
            <div class="card-header-custom">
                <h5><i class="fas fa-receipt text-gold me-1"></i> الطلبات</h5>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table dashboard-table mb-0">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td><a href="{{ route('sales.orders.show', $order->id) }}" class="text-gold">{{ $order->order_number }}</a></td>
                                    <td class="amount-cell">{{ CurrencyHelper::formatDual($order->total, $exchangeRate) }}</td>
                                    <td><span class="status-badge status-{{ $order->status }}">{{ $order->status }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($expenses->count() > 0)
        <div class="content-card mb-4">
            <div class="card-header-custom">
                <h5><i class="fas fa-file-invoice-dollar text-gold me-1"></i> المصروفات</h5>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table dashboard-table mb-0">
                        <thead>
                            <tr>
                                <th>الوصف</th>
                                <th>المبلغ</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $expense)
                                <tr>
                                    <td><a href="{{ route('expenses.edit', $expense->id) }}" class="text-gold">{{ $expense->description }}</a></td>
                                    <td class="amount-cell">{{ CurrencyHelper::formatDual($expense->amount, $exchangeRate) }}</td>
                                    <td>{{ $expense->date->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="content-card">
        <div class="card-body-custom">
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <p>لا توجد نتائج لـ "{{ $q }}"</p>
            </div>
        </div>
    </div>
@endif
@endsection
