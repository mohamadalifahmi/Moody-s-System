<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tables missing both created_by and updated_by
        $tablesMissingBoth = ['products', 'suppliers', 'orders', 'users', 'tenants'];
        foreach ($tablesMissingBoth as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        // Tables missing updated_by only (have created_by already)
        $tablesMissingUpdated = ['expenses', 'payments', 'invoices', 'stock_movements', 'purchases'];
        foreach ($tablesMissingUpdated as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tablesBoth = ['products', 'suppliers', 'orders', 'users', 'tenants'];
        foreach ($tablesBoth as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->dropColumn(['created_by', 'updated_by']);
            });
        }

        $tablesUpdated = ['expenses', 'payments', 'invoices', 'stock_movements', 'purchases'];
        foreach ($tablesUpdated as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn(['updated_by']);
            });
        }
    }
};

