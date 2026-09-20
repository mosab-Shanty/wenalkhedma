<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->unique(
                ['user_id', 'service_id'],
                'favorites_user_service_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique('favorites_user_service_unique');
        });
    }
};
