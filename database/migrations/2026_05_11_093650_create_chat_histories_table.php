<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('prompt');
            $table->text('response');
            $table->string('status')->default('completed'); // completed, failed, processing
            $table->string('model_used')->default('deepseek-v3');
            $table->integer('tokens_used')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_histories');
    }
};