<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manufacturing_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // MO-0001
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete(); // product jadi
            $table->foreignId('bom_id')->constrained('boms')->cascadeOnDelete();
            $table->decimal('qty_to_produce', 15, 3);
            $table->enum('status', ['DRAFT', 'DONE'])->default('DRAFT');
            $table->date('planned_date')->nullable();
            $table->date('done_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_orders');
    }
};
