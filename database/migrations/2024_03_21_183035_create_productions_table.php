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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->char('desc',255)->nullable();
            $table->char('type',50)->default('SAL');
            $table->integer('percentage')->nullable();
            $table->double('value')->default(0.0);
            $table->double('imponibile')->default(0.0);
            $table->date('date_start')->default(now());
            $table->date('date_end')->default(now());
            $table->char('status',50)->default('in corso');
            $table->tinyInteger('ft')->nullable();
            $table->date('date_ft')->nullable()->default(now());
            $table->text('note')->nullable();
            $table->unsignedBigInteger('client_id');

            $table->foreign('client_id')->references('id')->on('cli_fors');
            $table->foreignId('project_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
