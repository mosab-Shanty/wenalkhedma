<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('document_id')->primary();
            $table->foreignUuid('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignUuid('service_id')->constrained('services', 'service_id')->onDelete('cascade');
            $table->enum('file_type', ['image', 'document', 'license', 'other'])->default('image');
            $table->text('file_url');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
