<?php


namespace App\Http\Requests\User;



use App\Http\Requests\FormRequest;

class PostUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|min:3|required',
            'email' => 'email|unique:users|required',
            'password' => 'string|confirmed|required',
        ];
    }
}
