<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillingRequest extends FormRequest
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
        return [
            'sp3_id' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'no_registrasi' => ['required', 'string'],
            'eslon_id' => ['required', 'string'],
            'layanan_id' => ['required', 'string'],
            'sub_layanan_id' => ['nullable', 'string'],
            'tanggal_masuk' => ['required', 'string'],
            'tanggal_keluar' => ['required', 'string'],
            'biaya' => ['required', 'integer'],
        ];
    }
}
