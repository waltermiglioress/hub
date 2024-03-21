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
        Schema::create('tender_specs', function (Blueprint $table) {
            $table->id();
            $table->string('bu')->nullable()->default('pipeline');
            $table->string('fluid_type')->nullable()->default('metano');
            $table->smallInteger('lenght')->nullable();
            $table->smallInteger('inches')->nullable();
            $table->smallInteger('n_hdd')->nullable();
            $table->smallInteger('diameter_hdd')->nullable();
            $table->smallInteger('n_microt')->nullable();
            $table->smallInteger('diameter_microt')->nullable();
            $table->smallInteger('n_bvs')->nullable();

            $table->foreignId('tender_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_specs');
    }
};
