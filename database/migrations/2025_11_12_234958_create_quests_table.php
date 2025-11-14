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
            $table->foreignId('mob_id')->nullable()->constrained('mobs')->cascadeOnDelete();
            $table->foreignId('previous_quest_id')->nullable()->constrained('quests');
            $table->json('quest_chain')->nullable();
            $table->json('objectives');
            $table->json('steps');
            $table->json('raw_rewards')->nullable();
            $table->json('reward_types')->nullable();
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
