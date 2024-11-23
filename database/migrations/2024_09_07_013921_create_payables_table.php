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
        Schema::create('payables', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('tenant_id')->constrained();
            $table->unsignedBigInteger('sequential');
            $table->foreignUlid('supplier_id')->constrained();
            $table->foreignUlid('purchase_id')->nullable()->constrained();
            $table->date('due_date');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('paid_at')->nullable();
            $table->foreignUlid('paid_by')->nullable()->constrained('users');
            $table->enum('status', ['pending', 'paid'])->default('pending');
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
        Schema::dropIfExists('payables');
    }
};
