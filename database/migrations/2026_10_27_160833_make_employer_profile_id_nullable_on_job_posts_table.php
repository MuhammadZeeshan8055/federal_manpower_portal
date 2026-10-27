<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropForeign(['employer_profile_id']);
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE job_posts MODIFY employer_profile_id BIGINT UNSIGNED NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_posts ALTER COLUMN employer_profile_id DROP NOT NULL');
        } else {
            // sqlite / others: rebuild is handled loosely for local
            Schema::table('job_posts', function (Blueprint $table) {
                $table->unsignedBigInteger('employer_profile_id')->nullable()->change();
            });
        }

        Schema::table('job_posts', function (Blueprint $table) {
            $table->foreign('employer_profile_id')
                ->references('id')
                ->on('employer_profiles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropForeign(['employer_profile_id']);
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE job_posts MODIFY employer_profile_id BIGINT UNSIGNED NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_posts ALTER COLUMN employer_profile_id SET NOT NULL');
        } else {
            Schema::table('job_posts', function (Blueprint $table) {
                $table->unsignedBigInteger('employer_profile_id')->nullable(false)->change();
            });
        }

        Schema::table('job_posts', function (Blueprint $table) {
            $table->foreign('employer_profile_id')
                ->references('id')
                ->on('employer_profiles')
                ->cascadeOnDelete();
        });
    }
};
