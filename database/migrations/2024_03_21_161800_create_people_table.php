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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->char('firstname');
            $table->char('lastname');
            $table->string('tel')->nullable();
            $table->string('email')->nullable();
            $table->char('avatar')->nullable();
            $table->char('CF')->unique()->nullable();
            $table->string('address')->nullable();
            $table->foreignId('country_id')->constrained();
            $table->foreignId('state_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('cap')->nullable();
            $table->foreignId('clifor_id')->constrained('cli_fors','id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
