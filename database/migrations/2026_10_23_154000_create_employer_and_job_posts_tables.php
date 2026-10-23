<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('company_name', 160);
            $table->string('contact_person', 120);
            $table->string('phone', 40)->nullable();
            $table->string('country', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('rejection_reason', 255)->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_profile_id')->constrained('employer_profiles')->cascadeOnDelete();
            $table->string('title', 160);
            $table->string('category', 80)->nullable();
            $table->string('country', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('employment_type', 60)->nullable();
            $table->string('experience', 80)->nullable();
            $table->string('salary_text', 120)->nullable();
            $table->unsignedInteger('vacancies')->default(1);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('rejection_reason', 255)->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posts');
        Schema::dropIfExists('employer_profiles');
    }
};
