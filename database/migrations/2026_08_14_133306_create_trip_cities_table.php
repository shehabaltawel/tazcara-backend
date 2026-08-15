<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trip_cities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('trip_id')->constrained('trips');
            $table->foreignId('city_id')->constrained('cities');
            $table->unsignedSmallInteger('sequence');
            $table->decimal('price_from_origin', 8, 2);
            $table->dateTime('departure_timestamp');
            $table->dateTime('arrival_timestamp');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_cities');
    }
};
