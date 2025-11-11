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
    Schema::create('refunds', function (Blueprint $table) {
        $table->id();
        $table->foreignId('payment_id')->constrained()->cascadeOnDelete();

        $table->unsignedBigInteger('amount'); // partial or full in minor units

        $table->enum('status', [
            'requested',
            'completed',
            'failed'
        ])->default('requested');

        $table->timestamps();

        $table->index('payment_id');
        $table->index('status');
        $table->index('created_at');
    });
}



    /**
     * Reverse the migrations.
     */
 public function down(): void
{
    Schema::dropIfExists('refunds');
}

};
