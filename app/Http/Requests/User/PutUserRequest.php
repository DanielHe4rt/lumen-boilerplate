<?php


namespace App\Http\Requests\User;


use App\Http\Requests\FormRequest;

class PutUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|min:3',
            'email' => 'email|unique:users',
            'birthdate' => 'date',
            'password' => 'string'
        ];
    }
}
