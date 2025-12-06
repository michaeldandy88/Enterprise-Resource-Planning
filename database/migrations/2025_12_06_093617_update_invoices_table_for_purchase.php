<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('type')->default('SALES')->after('invoice_number'); // SALES, PURCHASE
            $table->foreignId('supplier_id')->nullable()->after('customer_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('purchase_order_id')->nullable()->after('sales_order_id')->constrained('purchase_orders')->onDelete('set null');
        });

        // Make customer_id nullable (MySQL syntax)
        DB::statement('ALTER TABLE invoices MODIFY customer_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['purchase_order_id']);
            $table->dropColumn(['type', 'supplier_id', 'purchase_order_id']);
        });
        
        // Revert customer_id to not null (Warning: might fail if nulls exist)
        DB::statement('ALTER TABLE invoices MODIFY customer_id BIGINT UNSIGNED NOT NULL');
    }
};
