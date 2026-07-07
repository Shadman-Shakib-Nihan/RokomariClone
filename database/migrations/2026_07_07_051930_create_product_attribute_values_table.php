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
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('attribute_id')
                ->constrained('attributes');

            $table->foreignId('attribute_option_id')
                ->nullable()
                ->constrained('attribute_options')
                ->nullOnDelete();

            $table->text('value_text')->nullable();

            $table->decimal('value_number', 12, 2)->nullable();

            $table->boolean('value_boolean')->nullable();

            $table->date('value_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};
