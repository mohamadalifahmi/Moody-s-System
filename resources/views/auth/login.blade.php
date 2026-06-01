@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="auth-brand">
                        <div class="auth-logo">
                            <svg viewBox="0 0 40 40" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="sg" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#d4a853"/>
                                        <stop offset="100%" stop-color="#a07820"/>
                                    </linearGradient>
                                </defs>
                                <path d="M20 3L35 10v13c0 8-15 14-15 14S5 31 5 23V10z" fill="url(#sg)"/>
                                <text x="20" y="26" text-anchor="middle" fill="#0c0c14" font-family="Arial,Helvetica,sans-serif" font-weight="900" font-size="18">M</text>
                            </svg>
                        </div>
                        <span class="auth-brand-name">Moody's</span>
                        <small style="display:block;font-size:10px;color:var(--gold);opacity:0.5;letter-spacing:3px;font-weight:600;margin-top:-2px;">MANAGEMENT</small>
                    </div>
                    <p class="auth-subtitle">تسجيل الدخول إلى لوحة التحكم</p>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-2">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-2 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt ms-1"></i>
                                {{ __('Login') }}
                            </button>
                            <a class="auth-link" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                        </div>

                        <div class="auth-footer">
                            <span>{{ __("Don't have an account?") }}</span>
                            <a href="{{ route('register') }}">{{ __('Register a new restaurant') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
