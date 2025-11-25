<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('service_providers');

            $table->string('service_name');
            $table->date('scheduled_date')->nullable();
            // Store time-of-day separately; no timezone conversion
            $table->time('scheduled_time')->nullable();
            $table->string('address')->nullable();

            $table->string('status', 20)->default('pending');
            $table->decimal('price', 10, 2)->nullable();
            $table->text('notes')->nullable();

            $table->unsignedTinyInteger('rating')->nullable();
            $table->timestamp('rated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['provider_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

