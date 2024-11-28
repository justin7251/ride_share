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
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->point('location');  // Remove nullable() to make it NOT NULL
            $table->timestamp('updated_at');
            $table->foreignId('current_ride_id')->nullable()->constrained('rides')->nullOnDelete();
            $table->spatialIndex('location');  // This will now work as location is NOT NULL
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
    
        // Drop foreign key constraints before dropping the table
        if (Schema::hasTable('driver_locations')) {
            Schema::table('driver_locations', function (Blueprint $table) {
                $table->dropForeign(['driver_id']);
                $table->dropForeign(['current_ride_id']);
            });
        }
        
        Schema::dropIfExists('driver_locations');
        Schema::enableForeignKeyConstraints();
    }
}; 