<?php

namespace App\Models;

use App\Models\Concerns\AssignsManualId;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PasalTatib extends Model
{
    use AssignsManualId;

    use Sluggable;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'nomor',
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

    protected $table = 'pasal_tatib';

    protected $fillable = [
        'nomor',
        'isi_ringkas',
        'aktif',
        'slug',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    public function jenisPelanggaran(): BelongsToMany
    {
        return $this->belongsToMany(
            JenisPelanggaran::class,
            'jenis_pelanggaran_pasal',
            'pasal_id',
            'jenis_pelanggaran_id'
        )->withTimestamps()->withPivot(['created_by', 'updated_by']);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nomor', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }
}
