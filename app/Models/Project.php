<?php

namespace App\Models;

use App\Concerns\HasTahunAktif;
use App\Concerns\HasUlids;
use App\Enums\JenisProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasUlids, SoftDeletes;
    use HasTahunAktif;

    protected $table = 'projects';

    protected $fillable = [
        'organization_id',
        'name',
        'type',
        'year',

        'nilai_kontrak',
        'nilai_dpp',
        'ppn',
        'pph',
        'nilai_ppn',
        'nilai_pph',
        'billing_ppn',
        'billing_pph',
        'ntpn_ppn',
        'ntpn_pph',

        'description',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'type' => JenisProject::class,
    ];

    public function organizations()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function projectContributors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_kontributor', 'project_id', 'kontributor_id');
    }

    public function getProgressPercentageAttribute(): float
    {
        $documents = $this->documents;

        if ($documents->isEmpty()) {
            return 0;
        }

        $totalProgress = 0;

        foreach ($documents as $document) {
            $totalProgress += $document->progress_percentage;
        }

        return round($totalProgress / $documents->count(), 2);
    }

    public function getIsCompleteAttribute(): bool
    {
        $documents = $this->documents;

        if ($documents->isEmpty()) {
            return false;
        }

        foreach ($documents as $document) {
            if (!$document->is_complete) {
                return false;
            }
        }

        return true;
    }
}
