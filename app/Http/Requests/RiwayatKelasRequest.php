<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RiwayatKelasRequest extends FormRequest
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
        // composite unique: siswa_id + tahun_ajaran_id
        $unique = Rule::unique('riwayat_kelas')->where(function ($query) {
            return $query->where('siswa_id', $this->input('siswa_id'))
                ->where('tahun_ajaran_id', $this->input('tahun_ajaran_id'));
        });

        if ($this->route('riwayat_kelas'))
            $unique->ignore($this->route('riwayat_kelas')->id);

        return [
            'siswa_id' => 'required|exists:siswa,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas' => [
                'required',
                'string',
                'max:255',
                $unique,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_id.required' => 'Siswa tidak boleh kosong.',
            'siswa_id.exists' => 'Siswa yang dipilih tidak ditemukan.',
            'tahun_ajaran_id.required' => 'Tahun ajaran tidak boleh kosong.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran yang dipilih tidak ditemukan.',
            'kelas.required' => 'Kolom kelas tidak boleh kosong.',
            'kelas.max' => 'Kolom kelas maksimal 255 karakter.',
            'kelas.unique' => 'Siswa sudah memiliki riwayat kelas pada tahun ajaran tersebut.',
        ];
    }
}
