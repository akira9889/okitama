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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('last_kana')->nullable();
            $table->string('first_kana')->nullable();
            $table->string('full_kana')->nullable();
            $table->string('company')->nullable();
            $table->foreignId('town_id')->constrained();
            $table->string('address_number')->nullable();
            $table->string('building_name')->nullable();
            $table->string('room_number')->nullable();
            $table->text('description')->nullable();
            $table->boolean('only_amazon')->default(false);

            $table->index(['full_name', 'town_id']);
            $table->index(['full_kana', 'town_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
