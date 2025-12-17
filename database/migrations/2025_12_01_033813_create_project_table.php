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
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->nullable()->index();
            $table->smallInteger('year')->nullable();
            $table->string('name')->nullable();

            $table->double('nilai_kontrak')->nullable();
            $table->double('nilai_dpp')->nullable();
            $table->double('nilai_ppn')->nullable();
            $table->double('nilai_pph')->nullable();

            $table->string('billing_ppn')->nullable();
            $table->string('billing_pph')->nullable();
            $table->double('ntpn_ppn')->nullable();
            $table->double('ntpn_pph')->nullable();

            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
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
