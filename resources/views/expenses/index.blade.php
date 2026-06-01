@extends('layouts.app')

@section('title', 'المصروفات')

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-hand-holding-usd text-gold me-2"></i> المصروفات</h1>
            <p class="text-muted">إدارة وتتبع جميع المصروفات</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('expenses.create') }}" class="btn btn-gold">
                <i class="fas fa-plus-circle"></i> إضافة مصروف جديد
            </a>
        </div>
    </div>
</div>

<div class="content-card mb-4">
    <div class="card-body-custom">
        <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="بحث عن بيان..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select">
                    <option value="">جميع التصنيفات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" placeholder="من تاريخ" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" placeholder="إلى تاريخ" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <select name="payment_method" class="form-select">
                    <option value="">جميع طرق الدفع</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                    <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>بنك</option>
                    <option value="other" {{ request('payment_method') == 'other' ? 'selected' : '' }}>آخر</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-teal w-100"><i class="fas fa-filter"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="content-card">
    <div class="card-body-custom p-0">
        @if($expenses->count() > 0)
            <div class="table-responsive">
                <table class="table dashboard-table mb-0">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>التصنيف</th>
                            <th>البيان</th>
                            <th>المبلغ</th>
                            <th>طريقة الدفع</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr>
                                <td class="time-cell">{{ $expense->date->format('Y-m-d') }}</td>
                                <td><span class="badge-info-custom">{{ $expense->category->name ?? '—' }}</span></td>
                                <td>{{ $expense->description }}</td>
                                <td class="amount-cell">{{ CurrencyHelper::formatDual($expense->amount, $exchangeRate) }}</td>
                                <td>
                                    @switch($expense->payment_method)
                                        @case('cash') نقدي @break
                                        @case('card') بطاقة @break
                                        @case('transfer') بنك @break
                                        @case('other') آخر @break
                                        @default {{ $expense->payment_method }}
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('expenses.edit', $expense->id) }}" class="btn-action" title="تعديل"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action" title="حذف"><i class="fas fa-trash text-danger"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body-custom border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">الإجمالي: <span class="amount-cell text-gold">{{ CurrencyHelper::formatDual($expenses->sum('amount'), $exchangeRate) }}</span></h5>
                    {{ $expenses->links() }}
                </div>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <p>لا توجد مصروفات بعد</p>
                <a href="{{ route('expenses.create') }}" class="btn btn-gold mt-3"><i class="fas fa-plus-circle"></i> إضافة مصروف جديد</a>
            </div>
        @endif
    </div>
</div>
@endsection
