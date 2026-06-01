@extends('layouts.app')

@section('title', 'الديون')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-balance-scale text-gold me-2"></i> الديون</h1>
            <p class="text-muted">إدارة الديون والالتزامات على المحل</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="content-card mb-4">
            <div class="card-header-custom">
                <h5><i class="fas fa-plus-circle text-gold me-1"></i> إضافة دين جديد</h5>
            </div>
            <div class="card-body-custom">
                <form method="POST" action="{{ route('debts.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">اسم الدائن <span class="text-danger">*</span></label>
                        <input type="text" name="creditor_name" class="form-control @error('creditor_name') is-invalid @enderror" value="{{ old('creditor_name') }}" placeholder="اسم الشخص أو الجهة">
                        @error('creditor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="0.00">
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المبلغ المدفوع</label>
                        <input type="number" step="0.01" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount', 0) }}" placeholder="0.00">
                        @error('paid_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاريخ الاستحقاق</label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}">
                        @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror" placeholder="وصف الدين">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" placeholder="ملاحظات إضافية">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> حفظ</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-list me-1"></i> الديون الحالية</h5>
            </div>
            <div class="card-body-custom p-0">
                @if($debts->count() > 0)
                    <div class="table-responsive">
                        <table class="table dashboard-table mb-0">
                            <thead>
                                <tr>
                                    <th>الدائن</th>
                                    <th>المبلغ</th>
                                    <th>المدفوع</th>
                                    <th>المتبقي</th>
                                    <th>تاريخ الاستحقاق</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($debts as $debt)
                                    <tr>
                                        <td class="fw-bold">{{ $debt->creditor_name }}</td>
                                        <td class="amount-cell">{{ CurrencyHelper::formatDual($debt->amount, $exchangeRate) }}</td>
                                        <td class="amount-cell">{{ CurrencyHelper::formatDual($debt->paid_amount, $exchangeRate) }}</td>
                                        <td class="amount-cell {{ $debt->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">{{ CurrencyHelper::formatDual($debt->remaining_amount, $exchangeRate) }}</td>
                                        <td class="text-muted">{{ $debt->due_date ? \Carbon\Carbon::parse($debt->due_date)->format('Y-m-d') : '—' }}</td>
                                        <td>
                                            @if($debt->status === 'active')
                                                <span class="status-badge status-pending"><i class="fas fa-clock"></i> نشط</span>
                                            @else
                                                <span class="status-badge status-completed"><i class="fas fa-check-circle"></i> مسدد</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn-action" data-bs-toggle="modal" data-bs-target="#editDebt{{ $debt->id }}" title="تعديل"><i class="fas fa-edit"></i></button>
                                            <form method="POST" action="{{ route('debts.destroy', $debt->id) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدين؟')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action" title="حذف"><i class="fas fa-trash text-danger"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body-custom border-top">{{ $debts->links() }}</div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-balance-scale"></i>
                        <p>لا توجد ديون مسجلة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@foreach($debts as $debt)
    <div class="modal fade" id="editDebt{{ $debt->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('debts.update', $debt->id) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit text-gold me-1"></i> تعديل الدين</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم الدائن <span class="text-danger">*</span></label>
                            <input type="text" name="creditor_name" class="form-control" value="{{ $debt->creditor_name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $debt->amount }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">المبلغ المدفوع</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control" value="{{ $debt->paid_amount }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تاريخ الاستحقاق</label>
                            <input type="date" name="due_date" class="form-control" value="{{ $debt->due_date ? \Carbon\Carbon::parse($debt->due_date)->format('Y-m-d') : '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" rows="2" class="form-control">{{ $debt->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" rows="2" class="form-control">{{ $debt->notes }}</textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="status" value="active">
                            <input type="checkbox" name="status" class="form-check-input" value="settled" id="dstatus{{ $debt->id }}" {{ $debt->status === 'settled' ? 'checked' : '' }}>
                            <label class="form-check-label" for="dstatus{{ $debt->id }}">مسدد بالكامل</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal"><i class="fas fa-times"></i> إلغاء</button>
                        <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
