<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengajuanPerubahanRequest extends FormRequest
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
                'pelanggaran_id' => 'required|exists:pelanggaran,id',
                'diajukan_oleh' => 'required|exists:users,id',
                'alasan' => 'required|string',
                'data_lama' => 'required|array',
                'data_baru' => 'required|array',
            ];

        return [
            'status' => 'required|string|in:menunggu,disetujui,ditolak',
            'diputuskan_oleh' => 'nullable|exists:users,id',
            'catatan_keputusan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'pelanggaran_id.required' => 'Pelanggaran tidak boleh kosong.',
            'pelanggaran_id.exists' => 'Pelanggaran yang dipilih tidak ditemukan.',
            'diajukan_oleh.required' => 'Pengaju tidak boleh kosong.',
            'diajukan_oleh.exists' => 'Pengaju tidak ditemukan.',
            'alasan.required' => 'Kolom alasan tidak boleh kosong.',
            'data_lama.required' => 'Kolom data lama tidak boleh kosong.',
            'data_lama.array' => 'Kolom data lama harus berupa data.',
            'data_baru.required' => 'Kolom data baru tidak boleh kosong.',
            'data_baru.array' => 'Kolom data baru harus berupa data.',
            'status.required' => 'Kolom status tidak boleh kosong.',
            'status.in' => 'Status hanya boleh berisi menunggu, disetujui, atau ditolak.',
            'diputuskan_oleh.exists' => 'Pemutus tidak ditemukan.',
        ];
    }
}
