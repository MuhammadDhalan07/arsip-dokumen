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
        Schema::table('rincians', function (Blueprint $table) {
            $table->decimal('bobot', 5, 2)->nullable()->default(0)->after('type')
                ->comment('Bobot dalam persen (0-100)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rincians', function (Blueprint $table) {
            $table->dropColumn('bobot');
        });
    }
};
