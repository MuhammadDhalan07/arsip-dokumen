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

    public function scopeTahunAktif(Builder $query): void
    {
        $tahun = ArsipDokumen::tahunAktif();
        $query->where('year', $tahun);
    }
}
