<?php

namespace App\Models;

use App\Concerns\HasTahunAktif;
use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasUlids, SoftDeletes;
    use HasTahunAktif;
    
    protected $table = 'projects';

    protected $fillable = [
        'name',
        'code',
        'year',
        'description',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

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
