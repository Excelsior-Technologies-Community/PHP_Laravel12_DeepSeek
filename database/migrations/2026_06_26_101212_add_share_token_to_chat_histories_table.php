<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chat_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_histories', 'share_token')) {
                $table->string('share_token')->nullable()->unique()->after('tokens_used');
            }
            if (!Schema::hasColumn('chat_histories', 'model_used')) {
                $table->string('model_used')->default('deepseek-chat')->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat_histories', function (Blueprint $table) {
            $table->dropColumn(['share_token']);
        });
    }
};