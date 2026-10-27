<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpSiswaRequest extends FormRequest
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
                'siswa_id' => 'required|exists:siswa,id',
                'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
                'level' => 'required|integer|min:1',
                'poin_saat_tercapai' => 'required|integer|min:0',
                'tanggal' => 'required|date',
            ];

        return [
            'siswa_id' => 'required|exists:siswa,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'level' => 'required|integer|min:1',
            'poin_saat_tercapai' => 'required|integer|min:0',
            'tanggal' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_id.required' => 'Siswa tidak boleh kosong.',
            'siswa_id.exists' => 'Siswa yang dipilih tidak ditemukan.',
            'tahun_ajaran_id.required' => 'Tahun ajaran tidak boleh kosong.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran yang dipilih tidak ditemukan.',
            'level.required' => 'Kolom level SP tidak boleh kosong.',
            'level.integer' => 'Kolom level SP harus berupa angka.',
            'level.min' => 'Kolom level SP minimal 1.',
            'poin_saat_tercapai.required' => 'Kolom poin saat tercapai tidak boleh kosong.',
            'poin_saat_tercapai.integer' => 'Kolom poin saat tercapai harus berupa angka.',
            'poin_saat_tercapai.min' => 'Kolom poin saat tercapai minimal 0.',
            'tanggal.required' => 'Kolom tanggal tidak boleh kosong.',
            'tanggal.date' => 'Kolom tanggal harus berupa tanggal yang valid.',
        ];
    }
}
