<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->uuid('favorite_id')->primary();
            $table->foreignUuid('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignUuid('service_id')->constrained('services', 'service_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
