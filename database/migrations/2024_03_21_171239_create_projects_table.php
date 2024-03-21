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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->char('code')->unique();
            $table->char('group')->nullable();
            $table->char('desc')->nullable();
            $table->char('contract')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();



            $table->foreignId('clifor_id')->constrained('cli_fors','id');
            $table->foreignId('tender_id')->constrained();
            $table->foreignId('responsible_id')->constrained('people','id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
