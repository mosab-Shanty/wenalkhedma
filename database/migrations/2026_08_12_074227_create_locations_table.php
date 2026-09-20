<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('location_id')->primary();
            $table->foreignUuid('service_id')->constrained('services', 'service_id')->onDelete('cascade');
            $table->string('governorate')->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            $table->text('address_text')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->geometry('geo_point', subtype: 'point')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
