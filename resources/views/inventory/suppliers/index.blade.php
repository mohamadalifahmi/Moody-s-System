@extends('layouts.app')

@section('title', 'الموردين')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-handshake text-gold me-2"></i> الموردين</h1>
            <p class="text-muted">إدارة الموردين</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="content-card mb-4">
            <div class="card-header-custom">
                <h5><i class="fas fa-plus-circle text-gold me-1"></i> إضافة مورد جديد</h5>
            </div>
            <div class="card-body-custom">
                <form method="POST" action="{{ route('inventory.suppliers.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="اسم المورد">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email@example.com">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الهاتف</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="رقم الهاتف">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان</label>
                        <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" placeholder="العنوان">{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> حفظ</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-list me-1"></i> الموردين الحاليين</h5>
            </div>
            <div class="card-body-custom p-0">
                @if($suppliers->count() > 0)
                    <div class="table-responsive">
                        <table class="table dashboard-table mb-0">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th>العنوان</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suppliers as $supplier)
                                    <tr>
                                        <td class="fw-bold">{{ $supplier->name }}</td>
                                        <td>{{ $supplier->email ?? '—' }}</td>
                                        <td dir="ltr">{{ $supplier->phone ?? '—' }}</td>
                                        <td class="text-muted">{{ Str::limit($supplier->address, 30) ?? '—' }}</td>
                                        <td>
                                            @if($supplier->is_active)
                                                <span class="status-badge status-completed"><i class="fas fa-check-circle"></i> نشط</span>
                                            @else
                                                <span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn-action" data-bs-toggle="modal" data-bs-target="#editSupplier{{ $supplier->id }}" title="تعديل"><i class="fas fa-edit"></i></button>
                                            <form method="POST" action="{{ route('inventory.suppliers.destroy', $supplier->id) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action" title="حذف"><i class="fas fa-trash text-danger"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body-custom border-top">{{ $suppliers->links() }}</div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-handshake"></i>
                        <p>لا يوجد موردين بعد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@foreach($suppliers as $supplier)
    <div class="modal fade" id="editSupplier{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('inventory.suppliers.update', $supplier->id) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit text-gold me-1"></i> تعديل المورد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الاسم <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" value="{{ $supplier->email }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الهاتف</label>
                            <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <textarea name="address" rows="2" class="form-control">{{ $supplier->address }}</textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" id="sactive{{ $supplier->id }}" {{ $supplier->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="sactive{{ $supplier->id }}">نشط</label>
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
