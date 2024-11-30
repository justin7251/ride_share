<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->boolean('is_started')->default(false);
            $table->boolean('is_complete')->default(false);
            $table->decimal('pickup_lat', 10, 8)->nullable();
            $table->decimal('pickup_lng', 11, 8)->nullable();
            $table->decimal('destination_lat', 10, 8)->nullable();
            $table->decimal('destination_lng', 11, 8)->nullable();
            $table->json('origin')->nullable();
            $table->json('destination')->nullable();
            $table->json('driver_location')->nullable();
            $table->enum('status', ['pending', 'started', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('current_ride_id')->nullable()->constrained('rides')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['current_ride_id']);
            $table->dropColumn('current_ride_id');
        });
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('rides');
        Schema::enableForeignKeyConstraints();
    }
};
