<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TatibDokumenRequest extends FormRequest
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
                'nama_file' => 'required|string|max:255',
                'path' => 'required|string|max:255',
                'diunggah_oleh' => 'required|exists:users,id',
                'tanggal' => 'required|date',
            ];

        return [
            'nama_file' => 'required|string|max:255',
            'path' => 'required|string|max:255',
            'diunggah_oleh' => 'required|exists:users,id',
            'tanggal' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_file.required' => 'Kolom nama file tidak boleh kosong.',
            'nama_file.max' => 'Kolom nama file maksimal 255 karakter.',
            'path.required' => 'Kolom path dokumen tidak boleh kosong.',
            'path.max' => 'Kolom path dokumen maksimal 255 karakter.',
            'diunggah_oleh.required' => 'Pengunggah dokumen tidak boleh kosong.',
            'diunggah_oleh.exists' => 'Pengunggah dokumen tidak ditemukan.',
            'tanggal.required' => 'Kolom tanggal tidak boleh kosong.',
            'tanggal.date' => 'Kolom tanggal harus berupa tanggal yang valid.',
        ];
    }
}
