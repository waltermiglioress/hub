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
        Schema::create('performances', function (Blueprint $table) {
            $table->id();
            $table->char('period');
            $table->double('pic')->default(0)->nullable();
            $table->double('add')->default(0)->nullable();
            $table->double('pdo')->default(0)->nullable();
            $table->double('cnc')->default(0)->nullable();
            $table->double('fob')->default(0)->nullable();
            $table->double('tbt_ail')->default(0)->nullable();
            $table->double('swa')->default(0)->nullable();
            $table->double('swar')->default(0)->nullable();
            $table->double('audit')->default(0)->nullable();
            $table->double('infortuni')->default(0)->nullable();
            $table->double('fac')->default(0)->nullable();
            $table->double('near_miss')->default(0)->nullable();
            $table->double('ua_uc')->default(0)->nullable();
            $table->double('drills')->default(0)->nullable();
            $table->double('vir')->default(0)->nullable();
            $table->double('ass_pnt')->default(0)->nullable();
            $table->double('dec_pnt')->default(0)->nullable();
            $table->double('cse_sorv')->default(0)->nullable();
            $table->double('nc_cse_sorv')->default(0)->nullable();
            $table->double('isp_hse_aspp')->default(0)->nullable();
            $table->double('isp_ext')->default(0)->nullable();
            $table->double('m_h')->default(0)->nullable();
            $table->timestamps();

            $table->foreignId('project_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performances');
    }
};
