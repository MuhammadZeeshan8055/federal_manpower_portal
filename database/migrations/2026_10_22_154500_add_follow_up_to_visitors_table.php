<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('follow_up_status', 20)->default('pending')->after('remind_on');
            $table->text('follow_up_remarks')->nullable()->after('follow_up_status');
            $table->index('follow_up_status');
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex(['follow_up_status']);
            $table->dropColumn(['follow_up_status', 'follow_up_remarks']);
        });
    }
};
