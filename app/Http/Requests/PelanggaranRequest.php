<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PelanggaranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->method() === 'POST')
            return [
                'kasus_id' => 'required|exists:kasus,id',
                'siswa_id' => 'required|exists:siswa,id',
                'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
                'nama_pelanggaran' => 'required|string|max:255',
                'poin' => 'required|integer|min:0',
                'kelas' => 'required|string|max:255',
                'pasal' => 'required|string',
            ];

        return [
            'kasus_id' => 'required|exists:kasus,id',
            'siswa_id' => 'required|exists:siswa,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nama_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:0',
            'kelas' => 'required|string|max:255',
            'pasal' => 'required|string',
            'alasan_hapus' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'kasus_id.required' => 'Kasus tidak boleh kosong.',
            'kasus_id.exists' => 'Kasus yang dipilih tidak ditemukan.',
            'siswa_id.required' => 'Siswa tidak boleh kosong.',
            'siswa_id.exists' => 'Siswa yang dipilih tidak ditemukan.',
            'tahun_ajaran_id.required' => 'Tahun ajaran tidak boleh kosong.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran yang dipilih tidak ditemukan.',
            'nama_pelanggaran.required' => 'Kolom nama pelanggaran tidak boleh kosong.',
            'nama_pelanggaran.max' => 'Kolom nama pelanggaran maksimal 255 karakter.',
            'poin.required' => 'Kolom poin tidak boleh kosong.',
            'poin.integer' => 'Kolom poin harus berupa angka.',
            'poin.min' => 'Kolom poin minimal 0.',
            'kelas.required' => 'Kolom kelas tidak boleh kosong.',
            'kelas.max' => 'Kolom kelas maksimal 255 karakter.',
            'pasal.required' => 'Kolom pasal tidak boleh kosong.',
        ];
    }
}
