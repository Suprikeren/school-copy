<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class TahunAjaran extends Model
{
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

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama',
        'slug',
        'tgl_mulai',
        'tgl_selesai',
        'aktif',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_selesai' => 'date',
            'aktif' => 'boolean',
        ];
    }

    public function riwayatKelas(): HasMany
    {
        return $this->hasMany(RiwayatKelas::class, 'tahun_ajaran_id');
    }

    public function kasus(): HasMany
    {
        return $this->hasMany(Kasus::class, 'tahun_ajaran_id');
    }

    public function pelanggaran(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'tahun_ajaran_id');
    }

    public function spSiswa(): HasMany
    {
        return $this->hasMany(SpSiswa::class, 'tahun_ajaran_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }
}
