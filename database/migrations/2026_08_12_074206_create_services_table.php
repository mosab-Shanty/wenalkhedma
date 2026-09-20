<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('service_id')->primary();
            $table->foreignUuid('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignUuid('category_id')->constrained('service_categories', 'category_id')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('phone')->nullable();
            $table->enum('current_status', ['open', 'closed', 'crowded', 'unknown'])->default('unknown');
            $table->json('opening_hours')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
