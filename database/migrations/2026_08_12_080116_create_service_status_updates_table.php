<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_status_updates', function (Blueprint $table) {
            $table->uuid('update_id')->primary();
            $table->foreignUuid('service_id')->constrained('services', 'service_id')->onDelete('cascade');
            $table->enum('old_status', ['open', 'closed', 'crowded', 'unknown'])->nullable();
            $table->enum('new_status', ['open', 'closed', 'crowded', 'unknown']);
            $table->foreignUuid('updated_by')->constrained('users', 'user_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_status_updates');
    }
};
