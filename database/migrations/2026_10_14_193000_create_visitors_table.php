<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_no', 40);
            $table->string('purpose');
            $table->string('meet_with');
            $table->string('status', 30)->default('waiting');
            $table->timestamp('visited_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('visited_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
