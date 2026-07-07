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
    Schema::create('attributes', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100);
        $table->string('slug', 120)->unique();
        $table->string('input_type', 20)->default('text'); // text | number | select
        $table->string('unit', 20)->nullable(); // e.g. ml, GB, pages
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('attributes');
}
};
