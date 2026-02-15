<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangMasukRequest extends FormRequest
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
            'tanggal' => ['required', 'date'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'tipe' => ['required', 'in:lokal,impor'],
            'nomor_bc' => ['nullable', 'string', 'max:50'],
            'tanggal_bc' => ['nullable', 'date'],
            'pelabuhan' => ['nullable', 'string', 'max:100'],
            'negara_asal' => ['nullable', 'string', 'max:80'],
            'keterangan' => ['nullable', 'string'],
            'barang_id' => ['required', 'array', 'min:1'],
            'barang_id.*' => ['required', 'integer', 'exists:barang,id'],
            'qty' => ['required', 'array', 'min:1'],
            'qty.*' => ['required', 'integer', 'min:1'],
            'harga_beli' => ['required', 'array', 'min:1'],
            'harga_beli.*' => ['required', 'numeric', 'min:0'],
        ];
    }
}
