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
                                    <linearGradient id="alg" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0" stop-color="#232334"/>
                                        <stop offset="0.6" stop-color="#14141f"/>
                                        <stop offset="1" stop-color="#0a0a11"/>
                                    </linearGradient>
                                    <linearGradient id="ag" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0" stop-color="#f7df9b"/>
                                        <stop offset="0.4" stop-color="#e8bd4f"/>
                                        <stop offset="0.8" stop-color="#c9962b"/>
                                        <stop offset="1" stop-color="#9a6c12"/>
                                    </linearGradient>
                                </defs>
                                <rect x="0" y="0" width="40" height="40" rx="9.2" fill="url(#alg)"/>
                                <rect x="1.3" y="1.3" width="37.4" height="37.4" rx="7.8" fill="none" stroke="#e8bd4f" stroke-opacity="0.35" stroke-width="0.7"/>
                                <path d="M7.9 29.7 L7.9 14.9 L13.5 14.9 L20 24.5 L26.5 14.9 L32.2 14.9 L32.2 29.7 L26.6 29.7 L26.6 22.8 L20 28.8 L13.4 22.8 L13.4 29.7 Z" fill="url(#ag)"/>
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
