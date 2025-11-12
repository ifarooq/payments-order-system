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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->cascadeOnDelete();

        $table->enum('status', [
            'authorized',
            'captured',
            'voided',
            'refunded'
        ])->default('authorized');

        $table->unsignedBigInteger('amount'); // minor units
        $table->char('currency', 3)->default('MYR');
  $table->uuid('reference')->unique(); // <-- Add this line
        $table->timestamp('authorized_at')->nullable();
        $table->timestamp('captured_at')->nullable();
        $table->timestamp('voided_at')->nullable();
      
        $table->timestamps();

        $table->unique('order_id'); // one active payment per order
        $table->index('status');
        $table->index('created_at');
    });
}



    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::dropIfExists('payments');
}

};
