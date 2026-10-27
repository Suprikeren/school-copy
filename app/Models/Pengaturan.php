<?php

namespace App\Models;

use App\Models\Concerns\AssignsManualId;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use AssignsManualId;

    protected $table = 'pengaturan';

    protected $fillable = [
        'kunci',
        'nilai',
        'created_by',
        'updated_by',
    ];

    public static function ambil(string $kunci, ?string $default = null): ?string
    {
        return static::query()->where('kunci', $kunci)->value('nilai') ?? $default;
    }

    public static function ambangSp(): array
    {
        return [
            1 => (int) static::ambil('sp_1_poin', '30'),
            2 => (int) static::ambil('sp_2_poin', '60'),
            3 => (int) static::ambil('sp_3_poin', '90'),
        ];
    }
}
