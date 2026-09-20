<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_queries', function (Blueprint $table) {
            $table->uuid('query_id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->text('query_text');
            $table->string('detected_intent')->nullable();
            $table->foreignUuid('result_service_id')->nullable()->constrained('services', 'service_id')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_queries');
    }
};
