<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $domainPath = app_path('Domains');

        foreach (File::directories($domainPath) as $domain) {
            $providerPath = $domain . '/Providers/DomainServiceProvider.php';

            if (File::exists($providerPath)) {
                $domainNamespace = 'App\\Domains\\' . basename($domain) . '\\Providers\\DomainServiceProvider';
                $this->app->register($domainNamespace);
            }
        }
    }

    public function boot(): void
    {
        //
    }
}
