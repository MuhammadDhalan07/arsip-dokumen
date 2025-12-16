<?php

namespace App\Concerns;

use App\ArsipDokumen;
use Illuminate\Database\Eloquent\Builder;

trait HasTahunAktif
{
    public function bootHasTahunAktif(): void
    {
        static::creating(function ($model) {
            $model->year = $model->year ?: ArsipDokumen::tahunAktif();
        });
    }

    public function scopeTahunAktif(Builder $query)
    {
        $table = $this->getTable();
        $tahun = ArsipDokumen::tahunAktif();
    }
}
