<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
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
            'surat_jalan_id' => ['required', 'integer', 'exists:surat_jalan,id'],
            'tanggal' => ['required', 'date'],
            'diskon_persen' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ongkos_kirim' => ['nullable', 'numeric', 'min:0'],
            'dp' => ['nullable', 'numeric', 'min:0'],
            'pembayaran' => ['nullable', 'string', 'max:50'],
            'keterangan' => ['nullable', 'string'],
        ];
    }
}
