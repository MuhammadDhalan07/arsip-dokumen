<?php

namespace App\Models;

use App\Concerns\HasTahunAktif;
use App\Concerns\HasUlids;
use Closure;
use App\Enums\JenisOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Organization extends Model
{
    use HasUlids, HasTahunAktif, SoftDeletes;

    protected $table = 'organizations';

    protected $fillable = [
        'year',
        'id_induk',
        'id_organization',
        'kode_organization',
        'nama_organization',
        'jenis_organization',
    ];

    protected $casts = [
        'jenis_organization' => JenisOrganization::class,
    ];

    public function induk(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id_induk', 'id_organization');
    }

    public function subSkpd(): HasMany
    {
        return $this->hasMany(self::class, 'id_induk', 'id_organization');
    }

    public static function options(
        ?Closure $modifyQueryUsing = null,
        ?string $search = null,
        ?string $include = null,
        ?int $limit = 50
    ): array {
        $queryOptions = (new static)
            ->query()
            ->limit($limit)
            ->when($modifyQueryUsing, fn ($q) => $q)
            ->when($search, fn ($q) => $q->where('nama_organization', 'like', "%{$search}%"))
            ->when(
                $include,
                fn ($query) => $query->orderBy(
                    DB::raw(<<<SQL
                    IF(nama_skpd = "{$include}", 1, 0)
                SQL),
                    'DESC'
                )
            )->orderBy('nama_organization');

        return $queryOptions->pluck('nama_organization', 'id_organization')->toArray();
    }
}
