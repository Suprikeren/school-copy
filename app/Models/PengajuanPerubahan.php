<?php

namespace App\Models;

use App\Models\Concerns\AssignsManualId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanPerubahan extends Model
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

    protected $table = 'pengajuan_perubahan';

    protected $fillable = [
        'pelanggaran_id',
        'diajukan_oleh',
        'alasan',
        'data_lama',
        'data_baru',
        'status',
        'diputuskan_oleh',
        'catatan_keputusan',
        'waktu',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'data_lama' => 'array',
            'data_baru' => 'array',
            'waktu' => 'datetime',
        ];
    }

    public function pelanggaran(): BelongsTo
    {
        return $this->belongsTo(Pelanggaran::class, 'pelanggaran_id');
    }

    public function diajukanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function diputuskanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function pemutus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('alasan', 'like', '%'.$search.'%')
                    ->orWhereHas('diajukanOleh', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('pelanggaran.siswa', function ($query) use ($search) {
                        $query->where('nama', 'like', '%'.$search.'%')
                            ->orWhere('nis', 'like', '%'.$search.'%');
                    });
            });
        }

        return $query;
    }
}
