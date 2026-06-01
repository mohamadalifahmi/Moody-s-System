<?php

namespace App\Providers;

use App\Domains\Auth\Models\Tenant;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $exchangeRate = 89500;
            if (Auth::check()) {
                $tenant = Tenant::find(Auth::user()->tenant_id);
                $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;
            }
            $view->with('exchangeRate', (float) $exchangeRate);
        });
    }
}
