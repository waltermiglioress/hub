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
        Schema::create('sub_contracts', function (Blueprint $table) {
            $table->id()->startingValue(1100);

            // Relazione con il cliente (che è un record del modello CliFor)
            $table->foreignId('client_id')->nullable()->constrained('cli_fors')->onDelete('cascade');
            // Relazione con il fornitore (che è un altro record del modello CliFor)
            $table->foreignId('supplier_id')->constrained('cli_fors')->onDelete('cascade');

            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->char('referent');
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_contracts');
    }
};
