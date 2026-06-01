@extends('layouts.app')

@section('title', 'تصنيفات المنتجات')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-tags text-gold me-2"></i> تصنيفات المنتجات</h1>
            <p class="text-muted">إدارة تصنيفات المنتجات</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="content-card mb-4">
            <div class="card-header-custom">
                <h5><i class="fas fa-plus-circle text-gold me-1"></i> إضافة تصنيف جديد</h5>
            </div>
            <div class="card-body-custom">
                <form method="POST" action="{{ route('inventory.categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="اسم التصنيف">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror" placeholder="وصف التصنيف">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> حفظ</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-list me-1"></i> التصنيفات الحالية</h5>
            </div>
            <div class="card-body-custom p-0">
                @if($categories->count() > 0)
                    <div class="table-responsive">
                        <table class="table dashboard-table mb-0">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>الوصف</th>
                                    <th>عدد المنتجات</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td class="fw-bold">{{ $category->name }}</td>
                                        <td class="text-muted">{{ $category->description ?? '—' }}</td>
                                        <td><span class="badge-count">{{ $category->products_count }}</span></td>
                                        <td>
                                            @if($category->is_active)
                                                <span class="status-badge status-completed"><i class="fas fa-check-circle"></i> نشط</span>
                                            @else
                                                <span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn-action" data-bs-toggle="modal" data-bs-target="#editCategory{{ $category->id }}" title="تعديل"><i class="fas fa-edit"></i></button>
                                            <form method="POST" action="{{ route('inventory.categories.destroy', $category->id) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action" title="حذف"><i class="fas fa-trash text-danger"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body-custom border-top">{{ $categories->links() }}</div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-tags"></i>
                        <p>لا توجد تصنيفات بعد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@foreach($categories as $category)
    <div class="modal fade" id="editCategory{{ $category->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('inventory.categories.update', $category->id) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit text-gold me-1"></i> تعديل التصنيف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الاسم <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" rows="2" class="form-control">{{ $category->description }}</textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" id="pactive{{ $category->id }}" {{ $category->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="pactive{{ $category->id }}">نشط</label>
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
