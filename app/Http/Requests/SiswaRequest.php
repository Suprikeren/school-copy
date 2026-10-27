<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiswaRequest extends FormRequest
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
                'nis' => 'required|string|max:255|unique:siswa,nis',
                'nama' => 'required|string|max:255',
                'status' => 'required|string|max:255',
            ];

        return [
            'nis' => 'required|string|max:255|unique:siswa,nis,'.$this->route('siswa')->id,
            'nama' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required' => 'Kolom NIS tidak boleh kosong.',
            'nis.unique' => 'NIS sudah terdaftar, silahkan gunakan NIS lainnya.',
            'nama.required' => 'Kolom nama siswa tidak boleh kosong.',
            'nama.max' => 'Kolom nama siswa maksimal 255 karakter.',
            'status.required' => 'Kolom status tidak boleh kosong.',
        ];
    }
}
