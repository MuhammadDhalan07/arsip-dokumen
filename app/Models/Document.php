<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    use HasUlids, SoftDeletes, InteractsWithMedia;

    protected $table = 'documents';

    protected $fillable = [
        'project_id',
        // 'rincian_id',
        'pic_id',
        'title',
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
}
