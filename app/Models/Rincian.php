<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rincian extends Model
{
    use HasUlids;

    protected $table = 'rincians';

    protected $fillable = [
        'name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_rincian', 'rincian_id', 'document_id');
    }
}
