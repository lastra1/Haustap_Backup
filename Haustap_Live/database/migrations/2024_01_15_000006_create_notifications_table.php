<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info'); // info, warning, error, success
            $table->string('category')->nullable(); // booking, payment, system, chat, etc.
            $table->string('related_id')->nullable(); // ID of related entity (booking_id, etc.)
            $table->string('related_type')->nullable(); // Type of related entity
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index('user_id');
            $table->index(['user_id', 'is_read']);
            $table->index('category');
            $table->index('created_at');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};