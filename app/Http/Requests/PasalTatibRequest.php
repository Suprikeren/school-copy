<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PasalTatibRequest extends FormRequest
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
        if ($this->method() === 'POST') {
            return [
                'nomor' => 'required|string|max:255|unique:pasal_tatib,nomor|distinct',
                'isi_ringkas' => 'required|string',
                'aktif' => 'required|boolean',
            ];
        }

        $pasal = $this->route('pasal');

        return [
            'nomor' => 'required|string|max:255|unique:pasal_tatib,nomor,'.($pasal?->id ?? 'NULL'),
            'isi_ringkas' => 'required|string',
            'aktif' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor.required' => 'Kolom nomor pasal tidak boleh kosong.',
            'nomor.unique' => 'Nomor pasal sudah ada, silahkan gunakan nomor lainnya.',
            'isi_ringkas.required' => 'Kolom isi ringkas tidak boleh kosong.',
            'aktif.required' => 'Kolom aktif tidak boleh kosong.',
            'aktif.boolean' => 'Kolom aktif harus bernilai true atau false.',
        ];
    }
}
