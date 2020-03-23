<?php


namespace App\Http\Requests\User\Me;


use App\Http\Requests\FormRequest;

class PutMePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'old_password' => 'required',
            'password' => 'confirmed|required'
        ];
    }
}
