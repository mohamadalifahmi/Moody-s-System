<?php

use App\Domains\Auth\Controllers\DashboardController;
use App\Domains\Auth\Controllers\ForgotPasswordController;
use App\Domains\Auth\Controllers\LoginController;
use App\Domains\Auth\Controllers\ProfileController;
use App\Domains\Auth\Controllers\RegisterController;
use App\Domains\Auth\Controllers\ResetPasswordController;
use App\Domains\Auth\Controllers\SettingsController;
use App\Domains\Debts\Controllers\DebtController;
use App\Domains\Expenses\Controllers\ExpenseCategoryController;
use App\Domains\Expenses\Controllers\ExpenseController;
use App\Domains\Inventory\Controllers\ProductCategoryController;
use App\Domains\Inventory\Controllers\ProductController;
use App\Domains\Inventory\Controllers\PurchaseController;
use App\Domains\Inventory\Controllers\SupplierController;
use App\Domains\Invoicing\Controllers\InvoiceController;
use App\Domains\Reports\Controllers\ReportController;
use App\Domains\Sales\Controllers\OrderSessionController;
use App\Domains\Sales\Controllers\PaymentController;
use App\Domains\Sales\Controllers\SalesController;
use App\Domains\Search\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Profile
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

    // Search
    Route::get('search', [SearchController::class, 'index'])->name('search');

    // Sales - Orders
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('orders', [SalesController::class, 'index'])->name('orders.index');
        Route::get('orders/create', [SalesController::class, 'create'])->name('orders.create');
        Route::post('orders', [SalesController::class, 'store'])->name('orders.store');
        Route::get('orders/{id}', [SalesController::class, 'show'])->name('orders.show');
        Route::get('orders/{id}/edit', [SalesController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{id}', [SalesController::class, 'update'])->name('orders.update');
        Route::delete('orders/{id}', [SalesController::class, 'destroy'])->name('orders.destroy');
        Route::get('orders/{id}/print', [SalesController::class, 'print'])->name('orders.print');

        // Sales - Sessions
        Route::get('sessions', [OrderSessionController::class, 'index'])->name('sessions.index');
        Route::post('sessions', [OrderSessionController::class, 'store'])->name('sessions.store');
        Route::get('sessions/{id}', [OrderSessionController::class, 'show'])->name('sessions.show');
        Route::match(['post', 'patch'], 'sessions/{id}/close', [OrderSessionController::class, 'close'])->name('sessions.close');

        // Sales - Payments
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    });

    // Expenses
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        Route::get('{id}/edit', [ExpenseController::class, 'edit'])->name('edit');
        Route::put('{id}', [ExpenseController::class, 'update'])->name('update');
        Route::delete('{id}', [ExpenseController::class, 'destroy'])->name('destroy');

        Route::get('categories', [ExpenseCategoryController::class, 'index'])->name('categories.index');
        Route::post('categories', [ExpenseCategoryController::class, 'store'])->name('categories.store');
        Route::put('categories/{id}', [ExpenseCategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{id}', [ExpenseCategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Debts
    Route::prefix('debts')->name('debts.')->group(function () {
        Route::get('/', [DebtController::class, 'index'])->name('index');
        Route::post('/', [DebtController::class, 'store'])->name('store');
        Route::put('{id}', [DebtController::class, 'update'])->name('update');
        Route::delete('{id}', [DebtController::class, 'destroy'])->name('destroy');
    });

    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::get('categories', [ProductCategoryController::class, 'index'])->name('categories.index');
        Route::post('categories', [ProductCategoryController::class, 'store'])->name('categories.store');
        Route::put('categories/{id}', [ProductCategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{id}', [ProductCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('purchases/{id}', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::get('purchases/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
        Route::put('purchases/{id}', [PurchaseController::class, 'update'])->name('purchases.update');
        Route::delete('purchases/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    });

    // Invoicing
    Route::prefix('invoicing')->name('invoicing.')->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{id}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::get('invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::post('invoices/{id}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
        Route::post('invoices/{id}/mark-cancelled', [InvoiceController::class, 'markAsCancelled'])->name('invoices.mark-cancelled');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('profit-loss/{fromDate?}/{toDate?}', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('export-daily', [ReportController::class, 'exportDaily'])->name('export-daily');
        Route::get('export-profit-loss', [ReportController::class, 'exportProfitLoss'])->name('export-profit-loss');
    });
});
