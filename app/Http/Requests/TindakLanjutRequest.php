<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TindakLanjutRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'siswa_id' => 'required|exists:siswa,id',
            'sp_siswa_id' => 'nullable|exists:sp_siswa,id',
            'pelanggaran_id' => 'nullable|exists:pelanggaran,id',
            'tindakan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
            'status' => 'required|string|in:belum,selesai',
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_id.required' => 'Siswa tidak boleh kosong.',
            'siswa_id.exists' => 'Siswa yang dipilih tidak ditemukan.',
            'sp_siswa_id.exists' => 'SP siswa yang dipilih tidak ditemukan.',
            'pelanggaran_id.exists' => 'Pelanggaran yang dipilih tidak ditemukan.',
            'tindakan.required' => 'Kolom tindakan tidak boleh kosong.',
            'tindakan.max' => 'Kolom tindakan maksimal 255 karakter.',
            'tanggal.required' => 'Kolom tanggal tidak boleh kosong.',
            'tanggal.date' => 'Kolom tanggal harus berupa tanggal yang valid.',
            'status.required' => 'Kolom status tidak boleh kosong.',
            'status.in' => 'Status hanya boleh berisi belum atau selesai.',
        ];
    }
}
