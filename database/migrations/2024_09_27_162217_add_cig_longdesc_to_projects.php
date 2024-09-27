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
        Schema::table('projects', function (Blueprint $table) {
            $table->char('CIG',11)->nullable()->after('code_ind');
            $table->longText('long_desc')->nullable()->after('desc');
            $table->text('contractor')->nullable()->after('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('CIG');
            $table->dropColumn('long_desc');
            $table->dropColumn('contractor');
        });
    }
};
