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
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('external_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('level')->default(0);
            $table->json('required_class')->nullable();
            $table->string('quest_giver');
            $table->json('objectives');
            $table->json('steps');
            $table->json('rewards')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quests');
    }
};
