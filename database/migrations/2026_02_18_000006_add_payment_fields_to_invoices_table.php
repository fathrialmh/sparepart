<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('sisa');
            }
            if (!Schema::hasColumn('invoices', 'payment_amount')) {
                $table->decimal('payment_amount', 15, 2)->default(0)->after('payment_status');
            }
            if (!Schema::hasColumn('invoices', 'payment_date')) {
                $table->date('payment_date')->nullable()->after('payment_amount');
            }
            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable()->after('payment_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_amount', 'payment_date', 'due_date']);
        });
    }
};
