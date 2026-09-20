<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('report_id')->primary();
            $table->foreignUuid('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignUuid('service_id')->constrained('services', 'service_id')->onDelete('cascade');
            $table->enum('reported_status', ['open', 'closed', 'crowded', 'wrong_location']);
            $table->text('note')->nullable();
            $table->text('image_url')->nullable();
            $table->enum('report_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
