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
        $table->string('name');
        $table->unsignedBigInteger('price'); // minor units (e.g., 9900)
        $table->char('currency', 3)->default('MYR');
        $table->timestamps();

        $table->index('price');
        $table->index('currency');
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
