<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('number', 40);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
