<?php

namespace App\Models;

use App\Models\Concerns\AssignsManualId;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class SpSiswa extends Model
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

    protected $table = 'sp_siswa';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'level',
        'poin_saat_tercapai',
        'tanggal',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'poin_saat_tercapai' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'sp_siswa_id');
    }
}
