<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('driver_locations')) {
            Schema::create('driver_locations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
                $table->point('location')->nullable();
                $table->timestamp('updated_at');
                $table->foreignId('current_ride_id')->nullable()->constrained('rides')->nullOnDelete();
                $table->spatialIndex('location');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('driver_locations')) {
            Schema::table('driver_locations', function (Blueprint $table) {
                $table->dropForeign(['driver_id']);
                $table->dropForeign(['current_ride_id']);
            });
    
            Schema::dropIfExists('driver_locations');
        }
    }
}; 