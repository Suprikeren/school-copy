<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TahunAjaranRequest extends FormRequest
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
        $method = $this->method();

        if ($method === 'POST')
            return [
                'nama' => 'required|string|max:255',
                'tgl_mulai' => 'required|date',
                'tgl_selesai' => 'required|date|after:tgl_mulai',
                'aktif' => 'required|boolean',
            ];

        return [
            'nama' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'aktif' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Kolom nama tahun ajaran tidak boleh kosong.',
            'nama.max' => 'Kolom nama tahun ajaran maksimal 255 karakter.',
            'tgl_mulai.required' => 'Kolom tanggal mulai tidak boleh kosong.',
            'tgl_mulai.date' => 'Kolom tanggal mulai harus berupa tanggal yang valid.',
            'tgl_selesai.required' => 'Kolom tanggal selesai tidak boleh kosong.',
            'tgl_selesai.date' => 'Kolom tanggal selesai harus berupa tanggal yang valid.',
            'tgl_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'aktif.required' => 'Kolom aktif tidak boleh kosong.',
            'aktif.boolean' => 'Kolom aktif harus bernilai true atau false.',
        ];
    }
}
