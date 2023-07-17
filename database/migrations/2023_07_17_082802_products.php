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
        // product with name and price and image and description and category and status
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->nullable();
            $table->string('slug', 100)->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('image', 100)->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            // category just a string
            $table->string('category', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('products');

    }
};
