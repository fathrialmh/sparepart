<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuratJalanRequest extends FormRequest
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
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'alamat_kirim' => ['nullable', 'string'],
            'no_po' => ['nullable', 'string', 'max:60'],
            'no_polisi' => ['nullable', 'string', 'max:30'],
            'sopir' => ['nullable', 'string', 'max:80'],
            'tipe_pajak' => ['required', 'in:kena_pajak,tidak_kena_pajak'],
            'pembayaran' => ['nullable', 'string', 'max:50'],
            'keterangan' => ['nullable', 'string'],
            'barang_id' => ['required', 'array', 'min:1'],
            'barang_id.*' => ['required', 'integer', 'exists:barang,id'],
            'qty' => ['required', 'array', 'min:1'],
            'qty.*' => ['required', 'integer', 'min:1'],
            'harga' => ['required', 'array', 'min:1'],
            'harga.*' => ['required', 'numeric', 'min:0'],
        ];
    }
}
