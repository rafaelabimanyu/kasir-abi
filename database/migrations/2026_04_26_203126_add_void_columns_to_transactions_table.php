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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('status')->default('success')->after('tanggal');
            $table->foreignId('void_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->string('void_reason')->nullable()->after('void_by');
            $table->timestamp('void_at')->nullable()->after('void_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['void_by']);
            $table->dropColumn(['status', 'void_by', 'void_reason', 'void_at']);
        });
    }
};

