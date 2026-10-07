<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('trade_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('care_off_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('process_status_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('full_name');
            $table->string('height', 40)->nullable();
            $table->string('weight', 40)->nullable();
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('gender', 20);
            $table->date('date_of_birth');
            $table->string('marital_status', 20);
            $table->string('religion', 100);
            $table->string('cnic', 30);
            $table->string('place_of_birth')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('police_station')->nullable();
            $table->string('district')->nullable();
            $table->string('medical_fitness', 20)->nullable();
            $table->string('photo_path')->nullable();

            $table->string('passport_number', 50);
            $table->date('passport_issue_date');
            $table->date('passport_expiry');

            $table->string('degree')->nullable();
            $table->string('degree_year', 4)->nullable();
            $table->string('certification')->nullable();
            $table->string('certification_year', 4)->nullable();
            $table->string('board_university')->nullable();
            $table->string('languages')->nullable();
            $table->string('total_experience')->nullable();

            $table->string('source', 20)->default('direct');
            $table->string('overall_status', 20)->default('active');

            $table->timestamps();

            $table->unique('cnic');
            $table->index('passport_number');
            $table->index('overall_status');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
