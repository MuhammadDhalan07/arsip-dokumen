<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    use HasUlids, SoftDeletes, InteractsWithMedia;

    protected $table = 'documents';

    protected $fillable = [
        'project_id',
        'pic_id',
        'document_number',
        'status',
        'description',
        'created_by',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function rincians():BelongsToMany
    {
        return $this->belongsToMany(Rincian::class, 'document_rincian', 'document_id', 'rincian_id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        $rincians = $this->rincians;

        if ($rincians->isEmpty()) {
            return 0;
        }

        $totalBobot = 0;
        $completedBobot = 0;

        foreach ($rincians as $rincian) {
            $bobot = $rincian->bobot ?? 0;
            $totalBobot += $bobot;

            $collectionName = Str::snake($rincian->name);
            $hasDocument = $this->getMedia($collectionName)->isNotEmpty();

            if ($hasDocument) {
                $completedBobot += $bobot;
            }
        }

        if ($totalBobot == 0) {
            return 0;
        }

        return round(($completedBobot / $totalBobot) * 100, 2);
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->progress_percentage >= 100;
    }

    public function getProgressStatusAttribute(): string
    {
        $progress = $this->progress_percentage;

        return match(true) {
            $progress == 0 => 'Belum Mulai',
            $progress < 30 => 'Baru Dimulai',
            $progress < 70 => 'Dalam Progress',
            $progress < 100 => 'Hampir Selesai',
            default => 'Selesai',
        };
    }

    public function getProgressColorAttribute(): string
    {
        $progress = $this->progress_percentage;

        return match(true) {
            $progress == 0 => 'gray',
            $progress < 30 => 'red',
            $progress < 70 => 'yellow',
            $progress < 100 => 'blue',
            default => 'green',
        };
    }
}
