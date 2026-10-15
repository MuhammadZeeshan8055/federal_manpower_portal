<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->time('office_start');
            $table->time('office_end');
            $table->unsignedTinyInteger('required_hours')->default(8);
            $table->time('lunch_start')->nullable();
            $table->time('lunch_end')->nullable();
            $table->unsignedSmallInteger('break_minutes')->default(60);
            $table->string('office_ip_1', 45)->nullable();
            $table->string('office_ip_2', 45)->nullable();
            $table->string('office_ip_3', 45)->nullable();
            $table->unsignedTinyInteger('monthly_full_days')->default(1);
            $table->unsignedTinyInteger('monthly_half_days')->default(2);
            $table->unsignedTinyInteger('grace_minutes')->default(15);
            $table->timestamps();
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('work_date');
            $table->dateTime('check_in_at')->nullable();
            $table->dateTime('check_out_at')->nullable();
            $table->unsignedInteger('worked_minutes')->default(0);
            $table->string('status_color', 10)->nullable();
            $table->boolean('is_late')->default(false);
            $table->boolean('is_early')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'work_date']);
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('title', 120);
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('from_date');
            $table->date('to_date');
            $table->string('leave_type', 10)->default('full');
            $table->unsignedTinyInteger('full_days')->default(0);
            $table->unsignedTinyInteger('half_days')->default(0);
            $table->string('reason', 255)->nullable();
            $table->string('status', 20)->default('pending');
            $table->boolean('is_paid')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('module_key', 50)->nullable();
            $table->string('feature_key', 50)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'read_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('bypass_attendance')->default(false)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bypass_attendance');
        });

        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_settings');
    }
};
