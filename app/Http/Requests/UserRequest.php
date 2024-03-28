<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
        if (request()->isMethod('post')) {
            $passwordRule = 'required';
            $passwordLamaRule = 'sometimes';
            $cek = Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
            $this->redirect = 'user/create';
        } elseif (request()->isMethod('put')) {
            $passwordRule = 'sometimes';
            $passwordLamaRule = 'sometimes';
            if (request()->password_lama == '') {
                $cek = '';
            } else {
                $cek = Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised();
            }
            $this->redirect = 'user/' . Crypt::encrypt($this->user) . '/edit';
        }
        return [
            'username' => ['required', Rule::unique('users')->ignore($this->user)],
            'name' => ['required', Rule::unique('users')->ignore($this->user)],
            'password' => [$passwordRule, $cek],
            'password_lama' => [$passwordLamaRule],
            'confirmation_password' => [$passwordRule, 'same:password'],
            'kd_skpd' => ['required'],
            'tipe' => ['required'],
            'status_aktif' => ['required'],
            'role' => ['required'],
            'jabatan' => ['required'],
        ];
    }
}
