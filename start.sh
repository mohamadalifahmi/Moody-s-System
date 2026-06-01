#!/bin/bash
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --class=TenantSeeder --force
php artisan db:seed --class=UserSeeder --force
php artisan db:seed --class=ExpenseCategorySeeder --force
php artisan db:seed --class=ProductCategorySeeder --force
php artisan db:seed --class=ProductSeeder --force
php artisan db:seed --class=SupplierSeeder --force
php artisan serve --host=0.0.0.0 --port=$PORT
