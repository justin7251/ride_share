<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->string('license_number')->unique()->nullable();
            $table->json('vehicle_info')->nullable();
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->integer('total_rides')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
