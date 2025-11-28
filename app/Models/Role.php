<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Support\Str;

class Role extends \Spatie\Permission\Models\Role
{
    use HasUlids;

    protected $fillable = [
        'name',
        'label',
        'guard_name',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (Role $role) {
            $role->label = Str::headline($role->name);
        });

        static::updating(function (Role $role) {
            $role->label = Str::headline($role->name);
        });
    }
}
