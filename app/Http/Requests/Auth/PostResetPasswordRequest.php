<?php


namespace App\Http\Requests\Auth;


use App\Http\Requests\FormRequest;

class PostResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'token' => 'required|exists:tokens,id',
            'password' => 'required|confirmed',
        ];
    }
}
