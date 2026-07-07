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
        $table->id();
        $table->foreignId('category_id')->constrained('categories');
        $table->foreignId('brand_id')->nullable()->constrained('brands');
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->decimal('discount_price', 10, 2)->nullable();
        $table->string('sku', 64)->unique()->nullable();
        $table->integer('stock_quantity')->default(0);
        $table->string('status', 20)->default('active'); // active | draft | out_of_stock
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('products');
}
};
