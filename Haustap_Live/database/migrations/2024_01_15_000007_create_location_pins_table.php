<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('location_pins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 8); // -90 to 90 degrees
            $table->decimal('longitude', 11, 8); // -180 to 180 degrees
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('type')->default('custom'); // home, work, service, custom
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);
            $table->json('metadata')->nullable(); // Additional data like radius, tags, etc.
            $table->timestamps();
            
            $table->index('user_id');
            $table->index(['latitude', 'longitude']);
            $table->index('type');
            $table->index('is_active');
            $table->index('created_at');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('location_pins');
    }
};