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
        Schema::create('emergency_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('type'); // illness, cancellation, early_checkout
            $table->text('description');
            $table->string('status')->default('open'); // open, resolved, closed
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('refund_status')->default('pending'); // pending, processed, completed
            $table->json('emergency_details')->nullable(); // Additional emergency information
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_cases');
    }
};
