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
        Schema::create('cli_fors', function (Blueprint $table) {
            $table->id();
            $table->char('name');
            $table->char('avatar')->nullable();
            $table->char('piva',11)->unique()->nullable();
            $table->char('CF')->nullable();
            $table->boolean('client')->default(false);
            $table->string('address')->nullable();
            $table->foreignId('country_id')->constrained();
            $table->foreignId('state_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('cap')->nullable();
            $table->string('tel')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cli_fors');
    }
};
