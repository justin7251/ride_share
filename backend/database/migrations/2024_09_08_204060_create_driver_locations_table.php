<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->point('location')->nullable();
            $table->timestamp('updated_at');
            $table->foreignId('current_ride_id')->nullable()->constrained('rides')->nullOnDelete();
            
            // Add spatial index
            $table->spatialIndex('location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_locations');
    }
}; 