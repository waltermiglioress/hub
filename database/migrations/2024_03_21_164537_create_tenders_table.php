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
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->char('rdo');
            $table->char('group')->nullable();
            $table->char('cig')->nullable();
            $table->char('num')->unique();
            $table->foreignId('country_id')->constrained();
            $table->foreignId('state_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->boolean('type');    //I per internazionale N nazionale
            $table->date('date_in');
            $table->char('desc')->nullable();
            $table->boolean('inspection')->default(false);
            $table->dateTime('date_end')->nullable();
            $table->string('mode')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();


            $table->foreignId('clifor_id')->constrained('cli_fors','id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
