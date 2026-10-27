<?php

namespace App\Models;

use App\Models\Concerns\AssignsManualId;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPelanggaran extends Model
{
    use AssignsManualId;

    use Sluggable;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'nama',
            ],
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->user()->name;
                $model->updated_by = auth()->user()->name;
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->name;
            }
        });
    }

    protected $table = 'jenis_pelanggaran';

    protected $fillable = [
        'nama',
        'kategori',
        'poin',
        'aktif',
        'slug',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    public function pasalTatib(): BelongsToMany
    {
        return $this->belongsToMany(
            PasalTatib::class,
            'jenis_pelanggaran_pasal',
            'jenis_pelanggaran_id',
            'pasal_id'
        )->withTimestamps()->withPivot(['created_by', 'updated_by']);
    }

    public function pelanggaran(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'jenis_pelanggaran_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nama', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }
}
