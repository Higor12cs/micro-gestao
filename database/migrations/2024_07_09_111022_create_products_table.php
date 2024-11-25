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
        Schema::create('products', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('tenant_id')->constrained();
            $table->unsignedBigInteger('sequential');
            $table->string('name');
            $table->string('barcode')->nullable();
            $table->foreignUlid('section_id')->nullable()->constrained();
            $table->foreignUlid('group_id')->nullable()->constrained();
            $table->foreignUlid('brand_id')->nullable()->constrained();
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->decimal('minimum_stock', 10, 2)->default(0);
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('products');
    }
};
