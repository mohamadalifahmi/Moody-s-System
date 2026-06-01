@extends('layouts.app')

@section('title', 'إضافة مصروف جديد')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-plus-circle text-gold me-2"></i> إضافة مصروف جديد</h1>
            <p class="text-muted">تسجيل مصروف جديد في النظام</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-gold"><i class="fas fa-arrow-right"></i> العودة للمصروفات</a>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-body-custom">
        <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">الفئة <span class="text-danger">*</span></label>
                    <select name="expense_category_id" class="form-select @error('expense_category_id') is-invalid @enderror">
                        <option value="">اختر التصنيف</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('expense_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                        <input type="number" step="0.01" min="0" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="0.00">
                    </div>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">التاريخ <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}">
                    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                    <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>بنك</option>
                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>آخر</option>
                    </select>
                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">البيان <span class="text-danger">*</span></label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="وصف المصروف">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">السند (اختياري)</label>
                    <input type="file" name="receipt" class="form-control @error('receipt') is-invalid @enderror" accept="image/*">
                    @error('receipt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> حفظ</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-gold"><i class="fas fa-times"></i> إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
