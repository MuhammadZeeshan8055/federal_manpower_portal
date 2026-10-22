<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('outcome', 30)->default('immature')->after('meet_with');
            $table->text('remarks')->nullable()->after('outcome');
            $table->date('remind_on')->nullable()->after('remarks');
            $table->index('remind_on');
        });

        // Keep any old rows as immature (desk log)
        DB::table('visitors')->update(['outcome' => 'immature']);

        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('status', 30)->default('waiting')->after('meet_with');
            $table->index('status');
        });

        DB::table('visitors')->update(['status' => 'waiting']);

        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex(['remind_on']);
            $table->dropColumn(['outcome', 'remarks', 'remind_on']);
        });
    }
};
