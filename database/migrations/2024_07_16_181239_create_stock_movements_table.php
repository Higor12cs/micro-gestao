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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('tenant_id')->constrained();
            $table->foreignUlid('product_id')->constrained();
            $table->foreignUlid('order_item_id')->nullable()->constrained();
            $table->foreignUlid('trial_item_id')->nullable();
            $table->foreignUlid('purchase_item_id')->nullable()->constrained();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('type', ['order', 'trial', 'purchase', 'adjustment', 'initial']);
            $table->text('observation')->nullable();
            $table->foreignUlid('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
