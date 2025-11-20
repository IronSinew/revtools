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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('color_code');
            $table->string('terrain_color');
            $table->json('coordinates');
            $table->json('exits')->nullable();
            $table->foreignId('exit_region_id')->nullable()->constrained('regions')->cascadeOnDelete();
            $table->string('exit_region_direction')->nullable();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
