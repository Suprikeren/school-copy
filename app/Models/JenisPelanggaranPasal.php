<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class JenisPelanggaranPasal extends Model
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

    protected $table = 'jenis_pelanggaran_pasal';

    protected $fillable = [
        'jenis_pelanggaran_id',
        'pasal_id',
        'created_by',
        'updated_by',
    ];

    public function jenisPelanggaran(): BelongsTo
    {
        return $this->belongsTo(JenisPelanggaran::class, 'jenis_pelanggaran_id');
    }

    public function pasal(): BelongsTo
    {
        return $this->belongsTo(PasalTatib::class, 'pasal_id');
    }
}
