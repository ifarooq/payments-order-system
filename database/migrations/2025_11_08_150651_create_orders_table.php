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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        $table->enum('status', [
            'pending',
            'confirmed',
            'fulfilled',
            'cancelled'
        ])->default('pending');

        $table->unsignedBigInteger('amount'); // total in minor units
        $table->char('currency', 3)->default('MYR');

        $table->timestamps();

        $table->index('user_id');
        $table->index('status');
        $table->index('created_at');
    });
}



    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::dropIfExists('orders');
}
};
