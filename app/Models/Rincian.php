<?php

namespace App\Models;

use App\Concerns\HasUlids;
use App\Enums\JenisRincian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Rincian extends Model
{
    use HasUlids;

    protected $table = 'rincians';

    protected $fillable = [
        'name',
        'bobot',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'bobot' => 'decimal:2',
        'type' => JenisRincian::class,
    ];

    public static function rules(): array
    {
        return [
            'bobot' => 'required|numeric|min:0|max:100',
        ];
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_rincian', 'rincian_id', 'document_id');
    }

    public function getProgressPercentageAttribute(): float
    {
        return $this->calculateProgress('normalize');
    }

    public function calculateProgress(string $mode = 'normalize'): float
    {
        $rincians = $this->rincians;

        if ($rincians->isEmpty()) {
            return 0;
        }

        $totalBobot = $rincians->sum('bobot');
        $completedBobot = 0;

        foreach ($rincians as $rincian) {
            $collectionName = Str::snake($rincian->name);
            $hasDocument = $this->getMedia($collectionName)->isNotEmpty();

            if ($hasDocument) {
                $completedBobot += $rincian->bobot;
            }
        }

        return match ($mode) {
            'strict' => $this->calculateProgressStrict($totalBobot, $completedBobot),
            'normalize' => $this->calculateProgressNormalize($totalBobot, $completedBobot),
            'equal' => $this->calculateProgressEqual(),
            default => 0,
        };
    }

    private function calculateProgressStrict(float $totalBobot, float $completedBobot): float
    {
        if ($totalBobot != 100) {
            throw new \Exception("Total bobot rincian harus 100%, saat ini: {$totalBobot}%");
        }

        return round($completedBobot, 2);
    }

    private function calculateProgressNormalize(float $totalBobot, float $completedBobot): float
    {
        if ($totalBobot == 0) {
            return 0;
        }

        return round(($completedBobot / $totalBobot) * 100, 2);
    }

    private function calculateProgressEqual(): float
    {
        $rincians = $this->rincians;
        $totalRincian = $rincians->count();

        if ($totalRincian == 0) {
            return 0;
        }

        $completedCount = 0;

        foreach ($rincians as $rincian) {
            $collectionName = Str::snake($rincian->name);
            $hasDocument = $this->getMedia($collectionName)->isNotEmpty();

            if ($hasDocument) {
                $completedCount++;
            }
        }

        return round(($completedCount / $totalRincian) * 100, 2);
    }

    public function getTotalBobotAttribute(): float
    {
        return $this->rincians->sum('bobot');
    }

    public function getIsBobotValidAttribute(): bool
    {
        return abs($this->total_bobot - 100) < 0.01;
    }

    public function getBobotWarningAttribute(): ?string
    {
        $total = $this->total_bobot;

        if ($total == 0) {
            return 'Belum ada bobot yang diset';
        }

        if ($total < 100) {
            return "Total bobot kurang: {$total}% (seharusnya 100%)";
        }

        if ($total > 100) {
            return "Total bobot lebih: {$total}% (seharusnya 100%)";
        }

        return null;
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->progress_percentage >= 100;
    }

    public function getProgressStatusAttribute(): string
    {
        $progress = $this->progress_percentage;

        return match (true) {
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

        return match (true) {
            $progress == 0 => 'gray',
            $progress < 30 => 'red',
            $progress < 70 => 'yellow',
            $progress < 100 => 'blue',
            default => 'green',
        };
    }
}
