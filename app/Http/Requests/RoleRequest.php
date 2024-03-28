<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
        // dd($this->route('peran'));
        return [
            'name' => [
                'required', 'string',
                Rule::unique('roles', 'name')->ignore($this->route('peran'), 'uuid')
            ],
            'akses' => 'required|array',
            'akses.*' => 'required|string',
            'check1' => 'required|array',
        ];
    }
}
