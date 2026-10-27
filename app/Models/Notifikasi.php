<?php

namespace App\Models;

use App\Models\Concerns\AssignsManualId;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Notifikasi extends Model
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

    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'tipe',
        'isi',
        'tautan',
        'dibaca_pada',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'dibaca_pada' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
