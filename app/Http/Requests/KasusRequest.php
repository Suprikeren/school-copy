<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KasusRequest extends FormRequest
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
                'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
                'waktu' => 'required|date',
                'pencatat_id' => 'required|exists:users,id',
                'keterangan' => 'nullable|string',
                'foto_path' => 'nullable|string|max:255',
            ];

        return [
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'waktu' => 'required|date',
            'pencatat_id' => 'required|exists:users,id',
            'keterangan' => 'nullable|string',
            'foto_path' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran tidak boleh kosong.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran yang dipilih tidak ditemukan.',
            'waktu.required' => 'Kolom waktu tidak boleh kosong.',
            'waktu.date' => 'Kolom waktu harus berupa tanggal dan waktu yang valid.',
            'pencatat_id.required' => 'Pencatat tidak boleh kosong.',
            'pencatat_id.exists' => 'Pencatat tidak ditemukan.',
            'foto_path.max' => 'Path foto maksimal 255 karakter.',
        ];
    }
}
