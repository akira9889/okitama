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
            $table->string('first_name')->nullable();
            $table->string('last_name');
            $table->string('full_name');
            $table->foreignId('town_id')->constrained();
            $table->string('address_number')->nullable();
            $table->string('room_number')->nullable();
            $table->boolean('is_dropoff_possible');
            $table->text('description')->nullable();

            $table->index(['full_name', 'town_id']);
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
