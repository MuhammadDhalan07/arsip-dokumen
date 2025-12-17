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
        Schema::create('organizations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->smallInteger('year')->nullable()->index();
            $table->bigInteger('id_induk')->nullable()->index();
            $table->bigInteger('id_organization')->nullable()->index();
            $table->string('kode_organization')->nullable();
            $table->text('nama_organization')->nullable();
            $table->string('jenis_organization', 10)->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
