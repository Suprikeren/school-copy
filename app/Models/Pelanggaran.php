<?php

namespace App\Models;

use App\Models\Concerns\AssignsManualId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Pelanggaran extends Model
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
    use SoftDeletes;

    protected $table = 'pelanggaran';

    protected $fillable = [
        'kasus_id',
        'siswa_id',
        'tahun_ajaran_id',
        'jenis_pelanggaran_id',
        'nama_pelanggaran',
        'poin',
        'kelas',
        'pasal',
        'alasan_hapus',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
        ];
    }

    public function kasus(): BelongsTo
    {
        return $this->belongsTo(Kasus::class, 'kasus_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function jenisPelanggaran(): BelongsTo
    {
        return $this->belongsTo(JenisPelanggaran::class, 'jenis_pelanggaran_id');
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(PengajuanPerubahan::class, 'pelanggaran_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nama_pelanggaran', 'like', '%' . $search . '%')
                    ->orWhere('pasal', 'like', '%' . $search . '%')
                    ->orWhereHas('siswa', function ($query) use ($search) {
                        $query->where('nama', 'like', '%' . $search . '%')
                            ->orWhere('nis', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('kasus.pencatat', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        return $query;
    }
}
