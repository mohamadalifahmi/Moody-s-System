@extends('layouts.app')

@section('title', 'مرحباً')

@section('content')
<div class="text-center" style="animation: pageEnter 0.6s ease;">
    <div style="font-size: 64px; color: var(--gold); margin-bottom: 16px;">
        <i class="fas fa-utensils"></i>
    </div>
    <h1 style="color: var(--text-primary); font-size: 28px; font-weight: 700; margin-bottom: 8px;">{{ config('app.name', 'Moody\'s') }}</h1>
    <p style="color: var(--text-muted); margin-bottom: 32px;">نظام إدارة المطاعم</p>
    <div style="display: flex; gap: 12px; justify-content: center;">
        <a href="{{ route('login') }}" class="btn btn-gold" style="padding: 12px 32px;">
            <i class="fas fa-sign-in-alt me-2"></i> تسجيل الدخول
        </a>
        @if (Route::has('register'))
        <a href="{{ route('register') }}" class="btn btn-outline-gold" style="padding: 12px 32px;">
            <i class="fas fa-user-plus me-2"></i> إنشاء حساب
        </a>
        @endif
    </div>
</div>
@endsection
