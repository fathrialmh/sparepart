<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $id = $this->route('user')?->id ?? $this->route('id');

        return [
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . $id],
            'nama' => ['required', 'string', 'max:100'],
            'role' => ['required', 'in:admin,user'],
            'password' => ['nullable', 'string', 'min:6'],
        ];
    }
}
