<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('doc_key', 60);
            $table->string('label');
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->boolean('is_not_required')->default(false);
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'doc_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_documents');
    }
};
