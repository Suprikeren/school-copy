<?php

namespace App\Models;

use App\Models\Concerns\AssignsManualId;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Siswa extends Model
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

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nama',
        'status',
        'slug',
        'created_by',
        'updated_by',
    ];

    public function riwayatKelas(): HasMany
    {
        return $this->hasMany(RiwayatKelas::class, 'siswa_id');
    }

    public function pelanggaran(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'siswa_id');
    }

    public function spSiswa(): HasMany
    {
        return $this->hasMany(SpSiswa::class, 'siswa_id');
    }

    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'siswa_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }
}
