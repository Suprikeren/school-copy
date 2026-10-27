<?php

namespace App\Models;

use App\Models\Concerns\AssignsManualId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjut extends Model
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

    protected $table = 'tindak_lanjut';

    protected $fillable = [
        'siswa_id',
        'sp_siswa_id',
        'pelanggaran_id',
        'tindakan',
        'tanggal',
        'catatan',
        'status',
        'ditangani_oleh',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function spSiswa(): BelongsTo
    {
        return $this->belongsTo(SpSiswa::class, 'sp_siswa_id');
    }

    public function pelanggaran(): BelongsTo
    {
        return $this->belongsTo(Pelanggaran::class, 'pelanggaran_id');
    }

    public function ditanganiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('tindakan', 'like', '%'.$search.'%')
                    ->orWhereHas('siswa', function ($query) use ($search) {
                        $query->where('nama', 'like', '%'.$search.'%')
                            ->orWhere('nis', 'like', '%'.$search.'%');
                    });
            });
        }

        return $query;
    }
}
