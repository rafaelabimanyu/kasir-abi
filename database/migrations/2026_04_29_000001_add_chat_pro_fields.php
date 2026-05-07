<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add last_activity_at to users for online status tracking
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_activity_at')->nullable()->after('remember_token');
        });

        // Add indexes to messages for faster queries
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['sender_id', 'receiver_id'], 'messages_conversation_index');
            $table->index('read_at', 'messages_read_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_activity_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_conversation_index');
            $table->dropIndex('messages_read_at_index');
        });
    }
};
