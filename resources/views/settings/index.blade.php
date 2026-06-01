@extends('layouts.app')

@section('title', 'الإعدادات')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1>الإعدادات</h1>
            <p>إعدادات المنشأة</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="fas fa-cog text-gold me-1"></i> إعدادات المنشأة</h5>
    </div>
    <div class="card-body-custom">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf


            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">الاسم</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $settings->name ?? '') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $settings->email ?? '') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">الهاتف</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $settings->phone ?? '') }}" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">العملة</label>
                        <select name="currency" class="form-select @error('currency') is-invalid @enderror" required>
                            <option value="LBP" {{ old('currency', $settings->currency ?? '') == 'LBP' ? 'selected' : '' }}>LBP - ليرة لبنانية</option>
                            <option value="USD" {{ old('currency', $settings->currency ?? '') == 'USD' ? 'selected' : '' }}>USD - دولار أمريكي</option>
                            <option value="EUR" {{ old('currency', $settings->currency ?? '') == 'EUR' ? 'selected' : '' }}>EUR - يورو</option>
                        </select>
                        @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">المنطقة الزمنية</label>
                        <select name="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                            <option value="Asia/Beirut" {{ old('timezone', $settings->timezone ?? '') == 'Asia/Beirut' ? 'selected' : '' }}>(UTC+02:00) بيروت</option>
                            <option value="Asia/Damascus" {{ old('timezone', $settings->timezone ?? '') == 'Asia/Damascus' ? 'selected' : '' }}>(UTC+02:00) دمشق</option>
                            <option value="Asia/Amman" {{ old('timezone', $settings->timezone ?? '') == 'Asia/Amman' ? 'selected' : '' }}>(UTC+03:00) عمان</option>
                            <option value="Africa/Cairo" {{ old('timezone', $settings->timezone ?? '') == 'Africa/Cairo' ? 'selected' : '' }}>(UTC+02:00) القاهرة</option>
                            <option value="Asia/Riyadh" {{ old('timezone', $settings->timezone ?? '') == 'Asia/Riyadh' ? 'selected' : '' }}>(UTC+03:00) الرياض</option>
                            <option value="Asia/Dubai" {{ old('timezone', $settings->timezone ?? '') == 'Asia/Dubai' ? 'selected' : '' }}>(UTC+04:00) دبي</option>
                        </select>
                        @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">نوع المنشأة</label>
                        <select name="business_type" class="form-select @error('business_type') is-invalid @enderror">
                            @foreach(config('business.types', ['general' => 'عام']) as $key => $label)
                                <option value="{{ $key }}" {{ old('business_type', $settings->business_type ?? 'general') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('business_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">معدل الضريبة (%)</label>
                        <input type="number" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" value="{{ old('tax_rate', $settings->tax_rate ?? '') }}" min="0" max="100" step="0.01" required>
                        @error('tax_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">سعر الصرف ($ → ل.ل)</label>
                        <input type="number" name="settings[exchange_rate]" class="form-control @error('settings.exchange_rate') is-invalid @enderror" value="{{ old('settings.exchange_rate', $settings->settings['exchange_rate'] ?? '') }}" min="0" step="1" placeholder="89500">
                        @error('settings.exchange_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">العنوان</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $settings->address ?? '') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="fw-bold mb-3"><i class="fas fa-file-invoice text-gold me-1"></i> إعدادات الفاتورة</h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">نص الرأس</label>
                        <input type="text" name="invoice_header" class="form-control @error('invoice_header') is-invalid @enderror" value="{{ old('invoice_header', $settings->invoice_header ?? '') }}">
                        @error('invoice_header')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">نص التذييل</label>
                        <input type="text" name="invoice_footer" class="form-control @error('invoice_footer') is-invalid @enderror" value="{{ old('invoice_footer', $settings->invoice_footer ?? '') }}">
                        @error('invoice_footer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="text-start mt-4">
                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-save me-1"></i> حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection