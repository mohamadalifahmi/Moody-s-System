@extends('layouts.app')

@section('title', 'المنتجات')

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-box text-gold me-2"></i> المنتجات</h1>
            <p class="text-muted">إدارة المنتجات والمخزون</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('inventory.products.create') }}" class="btn btn-gold"><i class="fas fa-plus-circle"></i> إضافة منتج جديد</a>
        </div>
    </div>
</div>

<div class="content-card mb-4">
    <div class="card-body-custom">
        <form method="GET" action="{{ route('inventory.products.index') }}" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو SKU..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">جميع التصنيفات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-check form-switch mt-2">
                    <input type="checkbox" name="stock_status" value="low" class="form-check-input" id="lowStock" {{ request('stock_status') == 'low' ? 'checked' : '' }}>
                    <label class="form-check-label" for="lowStock">عرض المنتجات منخفضة المخزون فقط</label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-teal w-100"><i class="fas fa-filter"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="content-card">
    <div class="card-body-custom p-0">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table dashboard-table mb-0">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>التصنيف</th>
                            <th>سعر الشراء</th>
                            <th>سعر البيع</th>
                            <th>المخزون</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            @php
                                $stockClass = '';
                                if ($product->stock_quantity <= 0) $stockClass = 'table-danger';
                                elseif ($product->stock_quantity < 10) $stockClass = 'table-warning';
                            @endphp
                            <tr class="{{ $stockClass }}">
                                <td class="fw-bold">{{ $product->name }} @if($product->sku)<br><small class="text-muted">SKU: {{ $product->sku }}</small>@endif</td>
                                <td><span class="badge-info-custom">{{ $product->category->name ?? '—' }}</span></td>
                                <td class="amount-cell">{{ CurrencyHelper::formatDual($product->purchase_price, $exchangeRate) }}</td>
                                <td class="amount-cell">{{ CurrencyHelper::formatDual($product->sale_price, $exchangeRate) }}</td>
                                <td>
                                    <span class="{{ $product->stock_quantity < 10 ? 'badge-danger-custom' : 'badge-success-custom' }}">
                                        {{ number_format($product->stock_quantity, 2) }} {{ $product->unit }}
                                    </span>
                                </td>
                                <td>
                                    @if($product->is_active)
                                        <span class="status-badge status-completed"><i class="fas fa-check-circle"></i> نشط</span>
                                    @else
                                        <span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('inventory.products.edit', $product->id) }}" class="btn-action" title="تعديل"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{ route('inventory.products.destroy', $product->id) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action" title="حذف"><i class="fas fa-trash text-danger"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body-custom border-top">{{ $products->links() }}</div>
        @else
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>لا توجد منتجات بعد</p>
                <a href="{{ route('inventory.products.create') }}" class="btn btn-gold mt-3"><i class="fas fa-plus-circle"></i> إضافة منتج جديد</a>
            </div>
        @endif
    </div>
</div>
@endsection
