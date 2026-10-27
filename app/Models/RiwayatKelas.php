<?php

namespace App\Models;

use App\Models\Concerns\AssignsManualId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatKelas extends Model
{
    use AssignsManualId;

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

    protected $table = 'riwayat_kelas';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'kelas',
        'created_by',
        'updated_by',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('kelas', 'like', '%'.$search.'%')
                    ->orWhereHas('siswa', function ($query) use ($search) {
                        $query->where('nama', 'like', '%'.$search.'%')
                            ->orWhere('nis', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('tahunAjaran', function ($query) use ($search) {
                        $query->where('nama', 'like', '%'.$search.'%');
                    });
            });
        }

        return $query;
    }
}
