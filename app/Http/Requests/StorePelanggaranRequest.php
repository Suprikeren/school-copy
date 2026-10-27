<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePelanggaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,id',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_ids.required' => 'Pilih minimal satu siswa.',
            'siswa_ids.array' => 'Data siswa yang dipilih tidak valid.',
            'siswa_ids.min' => 'Pilih minimal satu siswa.',
            'siswa_ids.*.exists' => 'Siswa yang dipilih tidak ditemukan.',
            'jenis_pelanggaran_id.required' => 'Pilih jenis pelanggaran.',
            'jenis_pelanggaran_id.exists' => 'Jenis pelanggaran tidak ditemukan.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
            'foto.image' => 'Foto bukti harus berupa gambar.',
            'foto.max' => 'Ukuran foto bukti maksimal 2 MB.',
        ];
    }
}
