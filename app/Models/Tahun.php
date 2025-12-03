<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Tahun extends Model
{
    use HasUlids;

    protected $table = 'tahun';

    protected $fillable = [
        'year',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];
}
