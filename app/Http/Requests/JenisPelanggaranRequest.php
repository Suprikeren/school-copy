<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JenisPelanggaranRequest extends FormRequest
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
                'nama' => 'required|string|max:255',
                'kategori' => 'required|string|max:255',
                'poin' => 'required|integer|min:0',
                'aktif' => 'required|boolean',
            ];

        return [
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'poin' => 'required|integer|min:0',
            'aktif' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Kolom nama jenis pelanggaran tidak boleh kosong.',
            'nama.max' => 'Kolom nama jenis pelanggaran maksimal 255 karakter.',
            'kategori.required' => 'Kolom kategori tidak boleh kosong.',
            'kategori.max' => 'Kolom kategori maksimal 255 karakter.',
            'poin.required' => 'Kolom poin tidak boleh kosong.',
            'poin.integer' => 'Kolom poin harus berupa angka.',
            'poin.min' => 'Kolom poin minimal 0.',
            'aktif.required' => 'Kolom aktif tidak boleh kosong.',
            'aktif.boolean' => 'Kolom aktif harus bernilai true atau false.',
        ];
    }
}
