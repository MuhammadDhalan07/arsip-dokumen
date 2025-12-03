<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasUlids, SoftDeletes;
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

    // public function rincians()
    // {
    //     return $this->hasMany(Rincian::class);
    // }
}
