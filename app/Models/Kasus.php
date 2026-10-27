<?php

namespace App\Models;

use App\Models\Concerns\AssignsManualId;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Kasus extends Model
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

    protected $table = 'kasus';

    protected $fillable = [
        'tahun_ajaran_id',
        'waktu',
        'pencatat_id',
        'keterangan',
        'foto_path',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'waktu' => 'datetime',
        ];
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pencatat_id');
    }

    public function pelanggaran(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'kasus_id');
    }
}
