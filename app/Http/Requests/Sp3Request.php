<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Sp3Request extends FormRequest
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
            'jenis_sp3' => ['required', 'string'],
            'tgl_sp3' => ['required', 'date'],
            'jenis_surat' => ['required', 'string'],
            'nomor_tagihan' => ['required', 'string'],
            'tgl_terima_keu' => ['required', 'date'],
            'perihal_tagihan_id' => ['required', 'integer'],
            'ket_inv_pasien' => ['required', 'string'],
            'ket_inv_rs' => ['required', 'string'],
            'eslon_id' => ['required', 'integer'],
            'ket_pembayaran' => ['required', 'string'],
            'layanan_id' => ['required', 'integer'],
            'kota' => ['required', 'string'],
            'nama_rs' => ['required', 'string'],
            'dokter_rujukan' => ['nullable', 'string'],
            'cob' => ['nullable', 'integer'],
            'jenis_sp3' => ['nullable', 'string'],
            'kunjungan' => ['nullable', 'integer'],
            'pasien' => ['nullable', 'integer'],
            'total_tagihan' => ['nullable', 'numeric', 'min:0'],
            'tgl_masuk' => ['required', 'date'],
            'tgl_keluar' => ['required', 'date', 'after_or_equal:tgl_masuk'],
            'keterangan' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'tgl_keluar.after_or_equal' => 'Tanggal keluar harus sama dengan atau setelah tanggal masuk.',
            'total_tagihan.min' => 'Total tagihan harus berupa angka positif.',
        ];
    }
}
