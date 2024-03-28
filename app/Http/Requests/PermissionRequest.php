<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
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
            'name' => [
                'required', 'string',
                Rule::unique('permissions', 'name')->ignore($this->route('akse'), 'uuid')
            ],
            'parent' => 'required',
            'tipe' => 'required',
            'link' => 'string|nullable|required_unless:tipe,1|required_unless:parent,-',
        ];
    }
}
